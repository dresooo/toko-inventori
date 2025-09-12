{{-- Show Profil Modal --}}

{{-- Show Profil Modal --}}
<dialog id="profileModal" class="modal">
    <div class="modal-box p-10 max-w-md">
        <h3 class="font-bold text-lg text-center mb-5">
            <i class="fas fa-user-circle mr-2 text-primary"></i>My Profile
        </h3>

        <div class="card bg-base-100 border-3 border-base-300 mt-4">
            <div class="card-body p-4">
                <div class="space-y-4">
                    <div>
                        <h2 class="text-sm text-base-content/60 mb-2">Nama</h2>
                        <p id="profileName" class="font-semibold">-</p>
                    </div>
                    <hr class="border-base-300 border-1">
                    <div>
                        <h2 class="text-sm text-base-content/60 mb-2">Email</h2>
                        <p id="profileEmail" class="font-semibold">-</p>
                    </div>
                    <hr class="border-base-300 border-1">
                    <div>
                        <h2 class="text-sm text-base-content/60 mb-2">No. Telepon</h2>
                        <p id="profileTelp" class="font-semibold">-</p>
                    </div>
                    <hr class="border-base-300 border-1">
                    <div>
                        <h2 class="text-sm text-base-content/60 mb-2">Alamat</h2>
                        <p id="profileAlamat" class="font-semibold">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="modal-action flex justify-between items-center mt-10">
            <div class="flex gap-2">
                <button id="editProfileBtn" class="btn btn-primary flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i>
                    <span>Edit Profil</span>
                </button>
                <button onclick="logout()"
                    class=" btn btn-error btn-outline flex items-center justify-center gap-2 px-6 pr-7">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>

            <button onclick="document.getElementById('profileModal').close()" class="btn">
                Close
            </button>
        </div>
    </div>

    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>


{{-- EDIT PROFIL MODAL --}}

<dialog id="editProfileModal" class="modal">
    <div class="modal-box p-10 max-w-md">
        <h3 class="font-bold text-lg text-center mb-5">
            <i class="fas fa-user-edit mr-2 text-primary"></i>Edit Profil
        </h3>

        <form id="editProfileForm" class="space-y-4 mt-4">
            <div>
                <h2>Nama</h2>
                <input type="text" id="editName" class="input input-bordered w-full mt-3"
                    placeholder="Masukkan nama lengkap">
            </div>
            <div>
                <h2>Email</h2>
                <input type="email" id="editEmail" class="input input-bordered w-full mt-3"
                    placeholder="Masukkan email">
            </div>
            <div>
                <h2>No. Telepon</h2>
                <input type="tel" id="editTelp" class="input input-bordered w-full mt-3"
                    placeholder="Masukkan nomor telepon">
            </div>
            <div>
                <h2>Alamat</h2>
                <textarea id="editAlamat" class="textarea textarea-bordered w-full h-24 resize-none mt-3"
                    placeholder="Masukkan alamat lengkap"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="modal-action flex justify-between items-center">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>Simpan
                </button>
                <button type="button" onclick="document.getElementById('editProfileModal').close()" class="btn">
                    Batal
                </button>
            </div>
        </form>
    </div>

    <!-- Backdrop -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>