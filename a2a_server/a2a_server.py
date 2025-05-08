from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
import logging
import os
from dotenv import load_dotenv
import google.generativeai as genai
import json

# Initialize Flask app
app = Flask(__name__)
CORS(app, resources={r"/jsonrpc": {"origins": "http://localhost"}})

# Configure logging
logging.basicConfig(
    filename='a2a_server.log',
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Load environment variables from .env
load_dotenv()

# Configure Gemini API
api_key = os.getenv('GOOGLE_API_KEY')
if not api_key:
    logging.error("GOOGLE_API_KEY is missing in the environment variables")
    raise ValueError("GOOGLE_API_KEY is not set in the environment")
genai.configure(api_key=api_key, transport="rest")

# Connect to the database (MySQL)
def get_db_connection():
    try:
        conn = mysql.connector.connect(
            database=os.getenv('DB_NAME', 'web2_sql'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', 'your_password'),
            host=os.getenv('DB_HOST', '127.0.0.1'),
            port=os.getenv('DB_PORT', '3306')
        )
        logging.info("Kết nối cơ sở dữ liệu thành công")
        return conn
    except Exception as e:
        logging.error(f"Không thể kết nối đến cơ sở dữ liệu: {str(e)}")
        raise Exception("Không thể kết nối đến cơ sở dữ liệu")

# Fetch list of books
def get_books():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("""
            SELECT p.product_id, p.product_name, p.price, c.category_name
            FROM product p
            LEFT JOIN product_category pc ON p.product_id = pc.product_id
            LEFT JOIN category c ON pc.category_id = c.category_id
            WHERE p.status_id = 1
        """)
        books = cur.fetchall()
        cur.close()
        conn.close()
        logging.info("Lấy danh sách sách thành công")
        return [(b[0], b[1], b[2], b[3] if b[3] else 'Không có danh mục') for b in books]
    except Exception as e:
        logging.error(f"Lỗi khi lấy danh sách sách: {str(e)}")
        return []

# Fetch list of categories
def get_categories():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("""
            SELECT c.category_id, c.category_name, ct.type_name
            FROM category c
            JOIN category_type ct ON c.category_type_id = ct.category_type_id
            WHERE c.status_id = 1 AND ct.status_id = 1
        """)
        categories = cur.fetchall()
        cur.close()
        conn.close()
        logging.info("Lấy danh sách danh mục thành công")
        return [(c[0], c[1], c[2]) for c in categories]
    except Exception as e:
        logging.error(f"Lỗi khi lấy danh sách danh mục: {str(e)}")
        return []

# Fetch list of category types
def get_category_types():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("SELECT type_name FROM category_type WHERE status_id = 1")
        category_types = [row[0] for row in cur.fetchall()]
        cur.close()
        conn.close()
        logging.info("Lấy danh sách loại danh mục thành công")
        return category_types
    except Exception as e:
        logging.error(f"Lỗi khi lấy danh sách loại danh mục: {str(e)}")
        return []

# Fetch list of book categories
def get_book_categories():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("SELECT DISTINCT category_name FROM category WHERE status_id = 1")
        categories = [row[0] for row in cur.fetchall() if row[0] is not None]
        cur.close()
        conn.close()
        logging.info("Lấy danh sách danh mục sách thành công")
        return categories
    except Exception as e:
        logging.error(f"Lỗi khi lấy danh sách danh mục sách: {str(e)}")
        return []

# Fetch total number of books
def get_total_books():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("SELECT COUNT(*) FROM product WHERE status_id = 1")
        total_books = cur.fetchone()[0]
        cur.close()
        conn.close()
        logging.info(f"Tổng số sách: {total_books}")
        return total_books
    except Exception as e:
        logging.error(f"Lỗi khi lấy tổng số sách: {str(e)}")
        return 0

# Fetch total number of category types
def get_total_category_types():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("SELECT COUNT(*) FROM category_type WHERE status_id = 1")
        total_category_types = cur.fetchone()[0]
        cur.close()
        conn.close()
        logging.info(f"Tổng số loại danh mục: {total_category_types}")
        return total_category_types
    except Exception as e:
        logging.error(f"Lỗi khi lấy tổng số loại danh mục: {str(e)}")
        return 0

# Fetch the most popular book
def get_most_popular_book():
    try:
        conn = get_db_connection()
        cur = conn.cursor()
        cur.execute("""
            SELECT 
                JSON_UNQUOTE(JSON_EXTRACT(product_details, '$[*].product_name')) AS product_names,
                JSON_UNQUOTE(JSON_EXTRACT(product_details, '$[*].quantity')) AS quantities,
                JSON_UNQUOTE(JSON_EXTRACT(product_details, '$[*].categories')) AS categories
            FROM sales_report
            WHERE status_id = 5
        """)
        sales_data = cur.fetchall()

        book_sales = {}
        for row in sales_data:
            product_names = json.loads(row[0]) if row[0] else []
            quantities = json.loads(row[1]) if row[1] else []
            categories = json.loads(row[2]) if row[2] else []

            for i in range(len(product_names)):
                book_name = product_names[i]
                quantity = int(quantities[i])
                category = categories[i][0] if categories[i] else "Không có danh mục"

                if book_name in book_sales:
                    book_sales[book_name]["quantity"] += quantity
                else:
                    book_sales[book_name] = {"quantity": quantity, "category": category}

        if not book_sales:
            cur.close()
            conn.close()
            logging.info("Không tìm thấy doanh số bán hàng hoàn thành trong hệ thống")
            return None

        most_popular = max(book_sales.items(), key=lambda x: x[1]["quantity"])
        book_name = most_popular[0]
        total_sold = most_popular[1]["quantity"]
        category = most_popular[1]["category"]

        cur.close()
        conn.close()
        logging.info(f"Sách phổ biến nhất: {book_name} trong danh mục {category} với {total_sold} đã bán")
        return (book_name, category, total_sold)
    except Exception as e:
        logging.error(f"Lỗi khi lấy sách phổ biến nhất: {str(e)}")
        return None

# Format price to VND
def format_price(price):
    try:
        # Chuyển giá thành định dạng số Việt Nam (ví dụ: 45000 -> 45.000)
        price_str = f"{int(price):,}".replace(",", ".")
        return f"{price_str} VND"
    except (TypeError, ValueError):
        return "Không có giá"

# Query Gemini API to answer user requests
def query_gemini(input_text, books, categories, category_types, book_categories, total_books, total_category_types, most_popular_book):
    try:
        # Create data lists for Gemini
        book_list = "\n".join([f"- {b[1]} (Giá: {format_price(b[2])}) trong danh mục {b[3]}" for b in books])
        category_list = "\n".join([f"- {c[1]} (Loại: {c[2]})" for c in categories])
        category_type_list = "\n".join([f"- {ct}" for ct in category_types])
        book_category_list = "\n".join([f"- {bc}" for bc in book_categories])

        # System information
        system_info = f"""
        Tổng số sách trong hệ thống: {total_books}
        Tổng số loại danh mục trong hệ thống: {total_category_types}
        """
        if most_popular_book:
            system_info += f"Sách phổ biến nhất (doanh số cao nhất): '{most_popular_book[0]}' trong danh mục {most_popular_book[1]} với {most_popular_book[2]} đã bán."

        # Create a flexible prompt in Vietnamese
        prompt = f"""
        Đầu vào của người dùng: {input_text}
        
        Dữ liệu có sẵn trong hệ thống:
        {system_info}
        
        Danh sách sách:
        {book_list}
        
        Danh sách danh mục:
        {category_list}
        
        Danh sách loại danh mục:
        {category_type_list}
        
        Danh sách danh mục sách:
        {book_category_list}
        
        Dựa trên yêu cầu của người dùng, hãy trả lời tự nhiên và rõ ràng bằng tiếng Việt. Người dùng có thể yêu cầu:
        - Gợi ý sách (ví dụ: "gợi ý một cuốn sách", "gợi ý 2 cuốn sách thuộc Tiểu thuyết", "gợi ý một cuốn sách dưới 50.000 VND").
        - Gợi ý danh mục (ví dụ: "gợi ý danh mục thuộc Khoa học", "gợi ý danh mục tiểu thuyết").
        - Thông tin loại danh mục (ví dụ: "có bao nhiêu loại danh mục?", "các loại danh mục trong hệ thống là gì?").
        - Thông tin sách (ví dụ: "có bao nhiêu sách?", "cuốn sách nào phổ biến nhất?").
        
        Hãy trả lời tự nhiên như trong một cuộc trò chuyện, đảm bảo chính xác dựa trên dữ liệu đã cung cấp. 
        Nếu người dùng yêu cầu nhiều sách hoặc danh mục, hãy liệt kê một cách tự nhiên. 
        Nếu không có dữ liệu phù hợp, hãy trả lời rõ ràng và tự nhiên bằng tiếng Việt.
        """

        # Use only gemini-1.5-flash
        available_models = [m.name for m in genai.list_models() if 'generateContent' in m.supported_generation_methods]
        logging.info(f"Các mô hình Gemini hỗ trợ generateContent: {available_models}")
        if 'models/gemini-1.5-flash-latest' not in available_models:
            raise ValueError("gemini-1.5-flash không khả dụng hoặc không hỗ trợ generateContent")
        model = genai.GenerativeModel('models/gemini-1.5-flash-latest')
        response = model.generate_content(prompt)
        answer = response.text.strip()
        
        logging.info(f"Phản hồi từ Gemini: {answer}")
        return answer
    except Exception as e:
        logging.error(f"Lỗi API Gemini: {str(e)}")
        return f"Xin lỗi, tôi không thể xử lý yêu cầu của bạn vì gemini-1.5-flash không khả dụng hoặc gặp lỗi: {str(e)}"

# Define JSON-RPC endpoint
@app.route('/jsonrpc', methods=['POST'])
def jsonrpc():
    try:
        # Get JSON data
        data = request.get_json()
        if not data or 'method' not in data or 'params' not in data:
            return jsonify({
                'jsonrpc': '2.0',
                'error': {'code': -32600, 'message': 'Yêu cầu không hợp lệ'},
                'id': data.get('id', None)
            }), 400

        method = data['method']
        params = data['params']
        request_id = data.get('id')

        # Handle recommend_book method
        if method == 'recommend_book':
            input_text = params.get('input', '')
            user_id = params.get('user_id')
            
            if not input_text or not user_id:
                return jsonify({
                    'jsonrpc': '2.0',
                    'error': {'code': -32602, 'message': 'Tham số không hợp lệ'},
                    'id': request_id
                }), 400

            # Fetch data from the database
            books = get_books()
            categories = get_categories()
            category_types = get_category_types()
            book_categories = get_book_categories()
            total_books = get_total_books()
            total_category_types = get_total_category_types()
            most_popular_book = get_most_popular_book()
            
            if not books and not categories:
                return jsonify({
                    'jsonrpc': '2.0',
                    'error': {'code': -32000, 'message': 'Không có sách hoặc danh mục nào khả dụng'},
                    'id': request_id
                }), 500

            # Call Gemini to generate a response
            response = query_gemini(input_text, books, categories, category_types, book_categories, total_books, total_category_types, most_popular_book)
            
            return jsonify({
                'jsonrpc': "2.0",
                'result': response,
                'id': request_id
            })

        else:
            return jsonify({
                'jsonrpc': '2.0',
                'error': {'code': -32601, 'message': 'Phương thức không tồn tại'},
                'id': request_id
            }), 404

    except Exception as e:
        logging.error(f"Lỗi server: {str(e)}")
        return jsonify({
            'jsonrpc': '2.0',
            'error': {'code': -32000, 'message': str(e)},
            'id': None
        }), 500

# Run the server on port 5001
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)