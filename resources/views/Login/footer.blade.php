<!-- jQuery -->
<script src="{{ asset('js/bootstrap/jquery-3.7.1.min.js') }}" type="text/javascript"></script>

<!-- Bootstrap Core JS -->
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}" type="text/javascript"></script>

<!-- Custom JS -->
<script src="{{ asset('js/bootstrap/script.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
<script>
    // Pure JavaScript SHA-256 implementation (works without crypto.subtle)
    function sha256(str) {
        function rightRotate(value, amount) {
            return (value >>> amount) | (value << (32 - amount));
        }
        
        const mathPow = Math.pow;
        const maxWord = mathPow(2, 32);
        const lengthProperty = 'length';
        let i, j;
        let result = '';
        
        const words = [];
        const asciiBitLength = str[lengthProperty] * 8;
        
        let hash = sha256.h = sha256.h || [];
        const k = sha256.k = sha256.k || [];
        let primeCounter = k[lengthProperty];
        
        const isComposite = {};
        for (let candidate = 2; primeCounter < 64; candidate++) {
            if (!isComposite[candidate]) {
                for (i = 0; i < 313; i += candidate) {
                    isComposite[i] = candidate;
                }
                hash[primeCounter] = (mathPow(candidate, .5) * maxWord) | 0;
                k[primeCounter++] = (mathPow(candidate, 1 / 3) * maxWord) | 0;
            }
        }
        
        str += '\x80';
        while (str[lengthProperty] % 64 - 56) str += '\x00';
        for (i = 0; i < str[lengthProperty]; i++) {
            j = str.charCodeAt(i);
            if (j >> 8) return;
            words[i >> 2] |= j << ((3 - i) % 4) * 8;
        }
        words[words[lengthProperty]] = ((asciiBitLength / maxWord) | 0);
        words[words[lengthProperty]] = (asciiBitLength);
        
        for (j = 0; j < words[lengthProperty];) {
            const w = words.slice(j, j += 16);
            const oldHash = hash;
            hash = hash.slice(0, 8);
            
            for (i = 0; i < 64; i++) {
                const w15 = w[i - 15], w2 = w[i - 2];
                const a = hash[0], e = hash[4];
                const temp1 = hash[7]
                    + (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25))
                    + ((e & hash[5]) ^ ((~e) & hash[6]))
                    + k[i]
                    + (w[i] = (i < 16) ? w[i] : (
                        w[i - 16]
                        + (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3))
                        + w[i - 7]
                        + (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10))
                    ) | 0
                    );
                const temp2 = (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22))
                    + ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2]));
                
                hash = [(temp1 + temp2) | 0].concat(hash);
                hash[4] = (hash[4] + temp1) | 0;
            }
            
            for (i = 0; i < 8; i++) {
                hash[i] = (hash[i] + oldHash[i]) | 0;
            }
        }
        
        for (i = 0; i < 8; i++) {
            for (j = 3; j + 1; j--) {
                const b = (hash[i] >> (j * 8)) & 255;
                result += ((b < 16) ? 0 : '') + b.toString(16);
            }
        }
        return result;
    }

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        if (!validateLogin()) {
            return; // stop AJAX if validation fails
        }
        const loginBtn = $('#loginButton');
        const originalText = loginBtn.html();
        loginBtn.prop('disabled', true).html('Logging in...');
        
        // Get form data
        const username = $('#username').val();
        const password = $('#password').val();
        
        $.ajax({
            url: "{{ route('login.submit') }}",
            method: "POST",
            data: {
                username: username,
                password: password,
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                toastr.success("Login successful! Redirecting...", "Success");

                setTimeout(function () {
                    window.location.href = response.redirect_to;
                }, 1500);
            },
            error: function (xhr) {
                loginBtn.prop('disabled', false).html(originalText);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0][0];
                    toastr.error(firstError, 'Validation Error');
                } else {
                    toastr.error("Login failed. Please try again.", 'Error');
                }
            }
        });
    });

    function validateLogin() {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value.trim();

        if (username === '') {
            toastr.warning('Please enter your Username / EMP ID.');
            document.getElementById('username').focus();
            return false;
        }
        if (password === '') {
            toastr.warning('Please enter your Password.');
            document.getElementById('password').focus();
            return false;
        }

        return true; // All good
    }
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.textContent = '🙈'; // Change icon to "hide"
        } else {
            passwordInput.type = 'password';
            toggleIcon.textContent = '👁️'; // Change icon to "view"
        }
    }
</script>
