// StudyMate - My Tasks: search, filter, and mark-as-completed (AJAX)
document.addEventListener("DOMContentLoaded", function () {

    const table = document.getElementById("tasks-table");
    const body = document.getElementById("tasks-body");
    const searchInput = document.getElementById("search-input");
    const statusFilter = document.getElementById("filter-status");
    const priorityFilter = document.getElementById("filter-priority");
    const csrf = table.dataset.csrf;

    let timer = null;

    function loadTasks() {
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            status: statusFilter.value,
            priority: priorityFilter.value
        });

        fetch("get_tasks.php?" + params.toString(), { credentials: "same-origin" })
            .then(function (res) {
                if (res.status === 401) { window.location.href = "login.html"; return null; }
                return res.json();
            })
            .then(function (tasks) { if (tasks) render(tasks); })
            .catch(function () {
                body.innerHTML = '<tr><td colspan="5">Could not load tasks.</td></tr>';
            });
    }

    function cell(text) {
        const td = document.createElement("td");
        td.textContent = text;
        return td;
    }

    function render(tasks) {
        body.innerHTML = "";

        if (tasks.length === 0) {
            const tr = document.createElement("tr");
            const td = cell("No tasks found.");
            td.colSpan = 5;
            tr.appendChild(td);
            body.appendChild(tr);
            return;
        }

        tasks.forEach(function (t) {
            const tr = document.createElement("tr");
            tr.appendChild(cell(t.title));
            tr.appendChild(cell(t.due_date));
            tr.appendChild(cell(t.priority));
            tr.appendChild(cell(t.status));

            const actions = document.createElement("td");

            // Edit link
            const edit = document.createElement("a");
            edit.href = "edit_task.php?id=" + t.task_id;
            edit.textContent = "Edit";
            actions.appendChild(edit);
            actions.appendChild(document.createTextNode(" "));

            // Mark completed / pending (AJAX)
            const toggle = document.createElement("button");
            toggle.type = "button";
            const newStatus = t.status === "Pending" ? "Completed" : "Pending";
            toggle.textContent = t.status === "Pending" ? "Mark Completed" : "Mark Pending";
            toggle.addEventListener("click", function () { changeStatus(t.task_id, newStatus); });
            actions.appendChild(toggle);
            actions.appendChild(document.createTextNode(" "));

            // Delete (same POST form as before, with CSRF token)
            const form = document.createElement("form");
            form.action = "delete_task.php";
            form.method = "post";
            form.style.display = "inline";
            form.addEventListener("submit", function (e) {
                if (!confirm("Are you sure you want to delete this task?")) e.preventDefault();
            });
            [["task_id", t.task_id], ["csrf_token", csrf]].forEach(function (pair) {
                const h = document.createElement("input");
                h.type = "hidden";
                h.name = pair[0];
                h.value = pair[1];
                form.appendChild(h);
            });
            const del = document.createElement("button");
            del.type = "submit";
            del.textContent = "Delete";
            form.appendChild(del);
            actions.appendChild(form);

            tr.appendChild(actions);
            body.appendChild(tr);
        });
    }

    function changeStatus(taskId, status) {
        const data = new URLSearchParams({ task_id: taskId, status: status, csrf_token: csrf });
        fetch("update_status.php", { method: "POST", body: data, credentials: "same-origin" })
            .then(function (res) { return res.json(); })
            .then(function (r) {
                if (r.success) loadTasks(); else alert(r.error || "Could not update status.");
            })
            .catch(function () { alert("Could not update status."); });
    }

    // live search (waits 250ms after the last key press)
    searchInput.addEventListener("input", function () {
        clearTimeout(timer);
        timer = setTimeout(loadTasks, 250);
    });
    statusFilter.addEventListener("change", loadTasks);
    priorityFilter.addEventListener("change", loadTasks);

    document.getElementById("clear-filters").addEventListener("click", function () {
        searchInput.value = "";
        statusFilter.value = "";
        priorityFilter.value = "";
        loadTasks();
    });

    loadTasks();
});
