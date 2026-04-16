
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch("../Api/dashboard.php");
        const data = await res.json();
        renderAlerts(data);
        console.log("Dashboard Data:", data);

        // Total Sales
        document.getElementById("totalSales").textContent =
            "₱" + (data.total_sales ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });

        // Total Solds
        document.getElementById("totalSolds").textContent =
            (data.total_solds ?? 0);

        // Transactions Today
        document.getElementById("totalTransactions").textContent =
            (data.transactions ?? 0);

        // Total Products
        const totalProductsEl = document.getElementById("totalProducts");
        totalProductsEl.textContent = (data.total_products ?? 0);

        // Highlight low stock
        if ((data.total_products ?? 0) <= (data.low_stock_threshold ?? 10)) {
            totalProductsEl.classList.add("low-stock");
        } else {
            totalProductsEl.classList.remove("low-stock");
        }

    } catch (e) {
        console.error("Failed to load dashboard metrics:", e);
    }
});
function renderAlerts(data) {
    const container = document.getElementById("alertsContainer");

    if (!container) {
        console.error("alertsContainer not found!");
        return;
    }

    const outList = data.out_stock_list ?? [];
    const lowList = data.low_stock_list ?? [];

    console.log("OUT LIST:", outList);

    let html = "";
    
    // ── Out of stock products
    outList.forEach(p => {
        html += `
        <div class="alert danger">
            <span class="icon">❗</span>
            <div>
                <strong>Out of Stock</strong><br>
                ${p.product_code} | ${p.product_type} | Qty: ${p.quantity}
            </div>
        </div>
        `;
        });
    // ── Low stock products
    lowList.forEach(p => {
        html += `
        <div class="alert warning">
            <span class="icon">⚠️</span>
            <div>
                <strong>Low Stock</strong><br>
                ${p.product_code} | ${p.product_type} | Qty: ${p.quantity}
            </div>
        </div>
        `;
        });

    container.innerHTML = html;
}