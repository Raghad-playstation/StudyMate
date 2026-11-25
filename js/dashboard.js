// StudyMate - dashboard counters + upcoming tasks (AJAX)
document.addEventListener("DOMContentLoaded", function () {

    fetch("get_stats.php", { credentials: "same-origin" })
        .then(function (res) {
            if (res.status === 401) {          // not logged in
                window.location.href = "login.html";
                return null;
            }
            return res.json();
        })
        .then(function (data) {
            if (!data) return;
            // here we gonna uppdate the html file with the vlues in the databae to show in the table
            document.getElementById("stat-total").textContent = data.total;
            document.getElementById("stat-pending").textContent = data.pending;
            document.getElementById("stat-completed").textContent = data.completed;

            const body = document.getElementById("upcoming-body");
            body.innerHTML = "";// to clear the vlues at html with the one in php 

            // in case no taks are there we will show no upcoming tasks
            if (data.upcoming.length === 0) {
                const tr = document.createElement("tr");
                const td = document.createElement("td");
                td.colSpan = 4;
                td.textContent = "No upcoming tasks.";
                tr.appendChild(td);
                body.appendChild(tr);
                return;
            }

            data.upcoming.forEach(function (t) {
                const tr = document.createElement("tr");
                [t.title, t.due_date, t.priority, t.status].forEach(function (value) {
                    const td = document.createElement("td");
                    td.textContent = value;      // textContent = safe from XSS
                    tr.appendChild(td);
                });
                body.appendChild(tr);
            });
        })
        .catch(function () {
            document.getElementById("upcoming-body").innerHTML =
                '<tr><td colspan="4">Could not load tasks.</td></tr>';
        });
});
