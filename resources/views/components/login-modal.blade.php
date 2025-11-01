<!-- resources/views/components/login-modal.blade.php -->

<dialog id="login_modal" class="modal">
    <div class="modal-box p-10 max-w-md">
        <h3 class="font-bold text-lg text-center mb-5">Login</h3>
        <form id="loginForm" class="space-y-4 mt-4">
            <h2>Email<span class="text-red-500"> *</span></h2>
            <input type="email" id="loginEmail" placeholder="Email" class="input input-bordered w-full" required />
            <h2>Password <span class="text-red-500">*</span></h2>
            <input type="password" id="loginPassword" placeholder="Password" class="input input-bordered w-full"
                required />
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>

        <h2 i class="mt-4 text-center">Don't have an account? <span id="openSignupModal"
                class="font-bold cursor-pointer hover:underline">Sign
                Up</span>
        </h2>
        <!-- Tombol close -->
        <div class="modal-action flex justify-between items-center">
            <button class="btn" onclick="document.getElementById('login_modal').close()">Close</button>
        </div>
    </div>

    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>