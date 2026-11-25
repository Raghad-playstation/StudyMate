
document.addEventListener("DOMContentLoaded", function () {

    // Show an error below an input
    function showError(input, message) {
        clearError(input);

        input.classList.add("input-error");
        input.style.setProperty("border", "2px solid #c0392b", "important");
        input.style.setProperty("background-color", "#fdecea", "important");

        const msg = document.createElement("small");
        msg.className = "error-msg";
        msg.style.color = "#c0392b";
        msg.style.display = "block";
        msg.textContent = message;

        input.insertAdjacentElement("afterend", msg);
    }

    // Remove an error
    function clearError(input) {
        input.classList.remove("input-error");
        input.style.removeProperty("border");
        input.style.removeProperty("background-color");

        const next = input.nextElementSibling;

        if (next && next.classList.contains("error-msg")) {
            next.remove();
        }
    }

    // Check email format
    function validEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    }

    // Apply validation rules to a form
    function attach(form, rules) {

        rules.forEach(function (r) {
            const el = document.getElementById(r.id);

            if (el) {
                el.addEventListener("input", function () {
                    clearError(el);
                });
            }
        });

        form.addEventListener("submit", function (e) {
            let ok = true;
            let firstBad = null;

            rules.forEach(function (r) {
                const el = document.getElementById(r.id);

                if (!el) return;

                const error = r.check(el.value.trim(), el.value);

                if (error) {
                    showError(el, error);
                    ok = false;

                    if (!firstBad) {
                        firstBad = el;
                    }
                } else {
                    clearError(el);
                }
            });

            if (!ok) {
                e.preventDefault();
                firstBad.focus();
            }
        });
    }

    // Registration validation
    const registerForm = document.querySelector('form[action="register.php"]');

    // we are using this to write the rules to use it in the attach 

    if (registerForm) {
        attach(registerForm, [ // here we used atach and give the reg form and write the rules as the second paramter ,,,, and it is array of rules 
            {
                id: "fullname",
                check: v => v.length < 3
                    ? "Full name must be at least 3 characters."
                    : ""
            },
            {
                id: "email",
                check: v => !validEmail(v)
                    ? "Please enter a valid email address."
                    : ""
            },
            {
                id: "password",
                check: (v, raw) =>
                    raw.length < 8
                        ? "Password must be at least 8 characters."
                        : !/[0-9]/.test(raw)
                            ? "Password must contain at least one number."
                            : ""
            },
            {
                id: "confirm-password",
                check: (v, raw) =>
                    raw !== document.getElementById("password").value
                        ? "Passwords do not match."
                        : ""
            }
        ]);
    }

    // Login validation
    const loginForm = document.querySelector('form[action="login.php"]');

    if (loginForm) {
        attach(loginForm, [
            {
                id: "email",
                check: v => !validEmail(v)
                    ? "Please enter a valid email address."
                    : ""
            },
            {
                id: "password",
                check: (v, raw) => raw === ""
                    ? "Please enter your password."
                    : ""
            }
        ]);

        if (new URLSearchParams(location.search).get("registered") === "1") {
            const ok = document.createElement("p");
            ok.textContent = "Account created successfully! Please log in.";
            ok.style.cssText =
                "color:#1e7e34;background:#e6f4ea;border:1px solid #1e7e34;padding:10px;border-radius:6px;margin-bottom:12px;";

            loginForm.insertAdjacentElement("beforebegin", ok);
        }
    }

    // Add Task validation
    const addTaskForm = document.querySelector('form[action="add_task.php"]');

    if (addTaskForm) {
        const dateInput = document.getElementById("due-date");

        function localDateString() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, "0");
            const day = String(now.getDate()).padStart(2, "0");
            return `${year}-${month}-${day}`;
        }

        const today = localDateString();

        if (dateInput) {
            dateInput.setAttribute("min", today);
        }

        attach(addTaskForm, [
            {
                id: "task-title",
                check: v => v.length < 3
                    ? "Task title must be at least 3 characters."
                    : ""
            },
            {
                id: "due-date",
                check: v =>
                    v === ""
                        ? "Please choose a due date."
                        : v < localDateString()
                            ? "Due date cannot be in the past."
                            : ""
            },
            {
                id: "priority",
                check: v => v === ""
                    ? "Please select a priority."
                    : ""
            }
        ]);
    }
});
