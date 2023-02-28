import './styles/app.css';
import './bootstrap';

document.addEventListener("DOMContentLoaded", function() {

    var rows = document.querySelectorAll("tr[data-href]");

    rows.forEach(function(row) {
        row.addEventListener("click", function() {

            var url = row.getAttribute("data-href");

            window.location.href = url;
        });
    });
});