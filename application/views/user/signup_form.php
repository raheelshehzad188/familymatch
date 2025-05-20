<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto-Fill Form</title>
</head>
<body>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Auto-Fill Form</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form id="signupForm" method="post" action="<?= base_url('user/signup/register'); ?>">
                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>

                    <!-- Age -->
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" required>
                    </div>

                    <!-- Gender -->
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="mb-3">
                        <label for="gender" class="form-label">Family Preference</label>
                        <select class="form-select" id="gender" name="family_preference" required>
                            <option value="Traditional">Traditional</option>
        <option value="Family-oriented">Family-oriented</option>
        <option value="Open-minded">Open-minded</option>
        <option value="Not interested">Not interested</option>
                        </select>
                    </div>

                    <!-- Bio -->
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" class="btn btn-primary w-100" onclick="autoFillForm('signupForm')">Auto-Fill Form</button>
                    <button type="submit" class="btn btn-primary w-100" onclick="autoFillForm('signupForm')">Sign up</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Function to fill form fields with dummy data based on input type
        function autoFillForm(formId) {
            const form = document.getElementById(formId);
            
            if (!form) {
                console.error('Form not found!');
                return;
            }

            const dummyData = {
                text: 'John Doe',           // Dummy name
                email: 'john.doe@example.com', // Dummy email
                password: 'password123',     // Dummy password
                number: '25',                // Dummy age
                textarea: 'Hello, this is a dummy bio.', // Dummy bio
                select: 'Male',             // Dummy gender
            };

            // Loop through each form field and fill it with appropriate dummy data
            Array.from(form.elements).forEach(input => {
                const inputType = input.type;
                const inputName = input.name || input.id;

                if (inputType === 'text' || inputType === 'password' || inputType === 'email' || inputType === 'number') {
                    input.value = dummyData[inputType] || '';
                } else if (inputType === 'textarea') {
                    input.value = dummyData.textarea || '';
                } else if (inputType === 'select-one') {
                    // For select elements (dropdowns)
                    const options = input.options;
                    for (let option of options) {
                        if (option.value === dummyData[inputType]) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            });
        }
        alert();
        autoFillForm('signupForm');
    </script>

</body>
</html>
