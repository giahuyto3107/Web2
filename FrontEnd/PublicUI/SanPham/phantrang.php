<?php

class Pagination {
    private $limit;
    private $page;
    private $offset;
    private $total_items;
    private $total_pages;

    public function __construct($limit = 8) {
        $this->limit = max(1, intval($limit)); // Đảm bảo limit không âm
        $this->page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $this->offset = ($this->page - 1) * $this->limit;
    }

    public function paginate($items) {
        // Lấy tổng số items từ danh sách đầu vào
        $this->total_items = count($items);
        $this->total_pages = ceil($this->total_items / $this->limit);

        // Cắt danh sách theo offset và limit
        $paginated_items = array_slice($items, $this->offset, $this->limit);

        return [
            'items' => $paginated_items,
            'total_pages' => $this->total_pages,
            'current_page' => $this->page,
            'total_items' => $this->total_items
        ];
    }

    public function getTotalPages() {
        return $this->total_pages;
    }

    public function getCurrentPage() {
        return $this->page;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function getLimit() {
        return $this->limit;
    }
}

?>