<div class="form">
<div class="form-title">
    <h2>Quản lý trang web</h2>
</div>


<div class="form-content">
    <div class="thongke-pn">
    <div class="label">
        <p>Thống kê đơn hàng theo: 7 ngày qua</p>
    </div>
    
    <select class="select-date" style="background-color: white">
        <option value="7">7 ngày qua</option>
        <option value="28">28 ngày qua</option>
        <option value="90">90 ngày qua</option>
        <option value="365">365 ngày qua</option>
    </select>

    <div class="chart-display">
        <p></p>
    </div>
    </div>
</div>

<script>
  const selectElement = document.querySelector(".select-date");
  const outputParagraph = document.querySelector(".thongke-pn p");

  selectElement.addEventListener("change", function() {
    outputParagraph.textContent = "Thống kê đơn hàng theo: " + this.value + " ngày qua";
  })
</script>

<style>
    body {
        background-color: #f8f9fa;
        display: flex;
        min-height: 100vh;
        padding: 20px;
        width: 100%;
    }

    .form {
            width: 100%;
            background: #e4e9f7;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .form-title {
        text-align: center;
    }

    .form-content {
        background-color: #e4e9f7;
        border-radius: 20px;
        width: 100%;
        margin: 10px;
        padding: 20px;
    }

    .thongke-pn {
      margin-top: 70px;
    }

    .label {
      font-size: 18px;
      color: #333;
      margin: 1rem 0;
      padding: 0.5rem;
      background-color: #f9f9f9;
      border-left: 4px solid #007bff;
      border-radius: 4px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .select-date {
      background-color: white;
      display: inline-block;
      background: var(--color-light);
      border-radius: var(--border-radius-1);
      margin-top: 1rem;
      padding: 0.5rem 1.6rem;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    .chart-display{
      height: 500px;
      background: rgb(255, 255, 255);
      padding: 1.8rem;
      border-radius: 2rem;
      box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 10px;
      margin: 20px;
      position: relative;
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }
</style>

