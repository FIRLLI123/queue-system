@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola User</h1>
        <p class="mb-0 text-gray-600 fs-7">Manajemen akun administrator dan staff Customer Care (CC).</p>
    </div>
    <div class="d-flex align-items-center" style="gap: 10px;">
        <button class="btn btn-outline-primary rounded-pill px-4" id="open-reorder-modal-btn">
            <i class="fas fa-sort mr-1"></i> Atur Urutan
        </button>
        <button class="btn btn-primary rounded-pill px-4" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-plus-circle mr-1"></i> Tambah User
        </button>
    </div>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="users-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="users-list-body">
                    <!-- Loaded dynamically via AJAX -->
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
                            <span>Memuat data user...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="addUserModalLabel">Tambah User Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-user-form">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-pill px-3" required placeholder="Contoh: Customer Care A1">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Username</label>
                        <input type="text" name="username" class="form-control rounded-pill px-3" required placeholder="Contoh: a1">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Email</label>
                        <input type="email" name="email" class="form-control rounded-pill px-3" required placeholder="Contoh: a1@ccqueue.com">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Password</label>
                        <input type="password" name="password" class="form-control rounded-pill px-3" required placeholder="Min. 6 Karakter">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-dark font-weight-bold fs-8">Role</label>
                                <select name="role" class="form-control rounded-pill px-3" style="height: 40px;" required>
                                    <option value="CC" selected>CEC (Customer Care)</option>
                                    <option value="ADMIN">ADMIN</option>
                                </select>
                                <div class="invalid-feedback px-2"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-dark font-weight-bold fs-8">Status</label>
                                <select name="status" class="form-control rounded-pill px-3" style="height: 40px;" required>
                                    <option value="ACTIVE" selected>ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                                <div class="invalid-feedback px-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-user-form">
                <input type="hidden" name="user_id" id="edit-user-id">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Nama Lengkap</label>
                        <input type="text" name="name" id="edit-user-name" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Username</label>
                        <input type="text" name="username" id="edit-user-username" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Email</label>
                        <input type="email" name="email" id="edit-user-email" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Password Baru</label>
                        <input type="password" name="password" class="form-control rounded-pill px-3" placeholder="Kosongkan jika tidak diganti">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-dark font-weight-bold fs-8">Role</label>
                                <select name="role" id="edit-user-role" class="form-control rounded-pill px-3" style="height: 40px;" required>
                                    <option value="CC">CEC (Customer Care)</option>
                                    <option value="ADMIN">ADMIN</option>
                                </select>
                                <div class="invalid-feedback px-2"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="text-dark font-weight-bold fs-8">Status</label>
                                <select name="status" id="edit-user-status" class="form-control rounded-pill px-3" style="height: 40px;" required>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                                <div class="invalid-feedback px-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 48px;"></i>
                <h5 class="font-weight-bold text-dark mb-2">Hapus User?</h5>
                <p class="text-secondary fs-8 mb-4">Tindakan ini tidak dapat dibatalkan. Antrian CEC terkait akan otomatis disesuaikan.</p>
                <input type="hidden" id="delete-user-id">
                <div class="d-flex justify-content-center gap-2" style="gap: 10px;">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger rounded-pill px-3 btn-sm">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.draggable-item {
    cursor: grab;
    transition: all 0.2s ease;
    user-select: none;
    border: 1px solid #e3e6f0;
}
.draggable-item:active {
    cursor: grabbing;
}
.draggable-item.dragging {
    opacity: 0.5;
    background-color: #f8f9fc;
    border: 1px dashed #4e73df;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: scale(0.98);
}
.draggable-item:hover:not(.dragging) {
    background-color: #f8f9fc;
    border-color: #bac8f3;
}
.drag-handle {
    color: #b7b9cc;
    cursor: grab;
    padding: 0 10px;
}
</style>

<!-- Reorder Queue Modal -->
<div class="modal fade" id="reorderQueueModal" tabindex="-1" role="dialog" aria-labelledby="reorderQueueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="reorderQueueModalLabel">Atur Urutan Antrian CC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3">
                <p class="text-secondary fs-8 mb-3">Tarik dan lepas (drag & drop) item untuk mengurutkan posisi antrian Customer Care. Posisi teratas akan menjadi yang pertama menerima order.</p>
                
                <div id="reorder-loading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
                    <span>Memuat antrian...</span>
                </div>
                
                <ul class="list-group" id="sortable-queue-list">
                    <!-- Draggable list items loaded via JS -->
                </ul>
                
                <div id="reorder-empty-msg" class="text-center py-4 text-secondary d-none">
                    Tidak ada staff CEC yang aktif saat ini.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-dismiss="modal">Batal</button>
                <button type="button" id="save-queue-order-btn" class="btn btn-primary rounded-pill px-4">Simpan Urutan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token header for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 1. Load Users List
    function loadUsers() {
        $.ajax({
            url: '/admin/users/data',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const tbody = $('#users-list-body');
                tbody.empty();

                if (data.users && data.users.length > 0) {
                    data.users.forEach((user, index) => {
                        const roleBadge = user.role === 'ADMIN' 
                            ? `<span class="badge badge-primary px-2.5 py-1.5 rounded-pill font-weight-bold">ADMIN</span>`
                            : `<span class="badge badge-info px-2.5 py-1.5 rounded-pill font-weight-bold">CC</span>`;
                        
                        const statusBadge = user.status === 'ACTIVE'
                            ? `<span class="badge badge-success px-2.5 py-1.5 rounded-pill font-weight-bold"><i class="fas fa-circle mr-1 fs-9 text-white pulse-green"></i>ACTIVE</span>`
                            : `<span class="badge badge-secondary px-2.5 py-1.5 rounded-pill font-weight-bold">INACTIVE</span>`;
                        
                        tbody.append(`
                            <tr>
                                <td class="font-monospace">${index + 1}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">${user.name}</div>
                                </td>
                                <td><span class="font-monospace text-secondary">@${user.username}</span></td>
                                <td>${user.email}</td>
                                <td>${roleBadge}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn btn-light btn-sm rounded-circle mr-1 edit-user-btn" data-id="${user.id}" title="Edit User">
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm rounded-circle delete-user-btn" data-id="${user.id}" title="Hapus User">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.html(`
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                Tidak ada data user ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                window.showToast('Gagal memuat data user.', 'error');
            }
        });
    }

    // Initial Load
    loadUsers();

    // Reset error styling on modal close
    $('.modal').on('hidden.bs.modal', function() {
        const form = $(this).find('form');
        if (form.length) {
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').empty();
        }
    });

    // 2. Create User Submission
    $('#add-user-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').empty();

        $.ajax({
            url: '/admin/users',
            method: 'POST',
            data: form.serialize(),
            success: function(data) {
                $('#addUserModal').modal('hide');
                window.showToast('User baru berhasil ditambahkan.', 'success');
                loadUsers();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        const input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    window.showToast('Terjadi kesalahan saat menyimpan user.', 'error');
                }
            }
        });
    });

    // 3. Edit User (Open Modal & Populate Form)
    $(document).on('click', '.edit-user-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/users/${id}`,
            method: 'GET',
            success: function(data) {
                const user = data.user;
                $('#edit-user-id').val(user.id);
                $('#edit-user-name').val(user.name);
                $('#edit-user-username').val(user.username);
                $('#edit-user-email').val(user.email);
                $('#edit-user-role').val(user.role);
                $('#edit-user-status').val(user.status);
                
                $('#editUserModal').modal('show');
            },
            error: function() {
                window.showToast('Gagal memuat detail user.', 'error');
            }
        });
    });

    // 4. Update User Submission
    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const id = $('#edit-user-id').val();
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').empty();

        $.ajax({
            url: `/admin/users/${id}`,
            method: 'PUT',
            data: form.serialize(),
            success: function(data) {
                $('#editUserModal').modal('hide');
                window.showToast('Detail user berhasil diperbarui.', 'success');
                loadUsers();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        const input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    window.showToast('Terjadi kesalahan saat memperbarui user.', 'error');
                }
            }
        });
    });

    // 5. Delete User Click
    $(document).on('click', '.delete-user-btn', function() {
        const id = $(this).data('id');
        $('#delete-user-id').val(id);
        $('#deleteUserModal').modal('show');
    });

    // 6. Delete User Confirmation
    $('#confirm-delete-btn').on('click', function() {
        const id = $('#delete-user-id').val();
        $.ajax({
            url: `/admin/users/${id}`,
            method: 'DELETE',
            success: function(data) {
                $('#deleteUserModal').modal('hide');
                window.showToast('User berhasil dihapus.', 'success');
                loadUsers();
            },
            error: function(xhr) {
                $('#deleteUserModal').modal('hide');
                const errMsg = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Gagal menghapus user.';
                window.showToast(errMsg, 'error');
            }
        });
    });

    // 7. Open Reorder Modal and load CC List
    $('#open-reorder-modal-btn').on('click', function() {
        $('#reorder-loading').removeClass('d-none');
        $('#sortable-queue-list').empty().addClass('d-none');
        $('#reorder-empty-msg').addClass('d-none');
        $('#reorderQueueModal').modal('show');

        $.ajax({
            url: '/admin/users/data',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#reorder-loading').addClass('d-none');
                
                const ccUsers = (data.users || []).filter(u => u.role === 'CC' && u.status === 'ACTIVE' && u.queue_position);
                // Sort CC users by their queue number ascending
                ccUsers.sort((a, b) => (a.queue_position ? a.queue_position.queue_number : 999) - (b.queue_position ? b.queue_position.queue_number : 999));

                if (ccUsers.length === 0) {
                    $('#reorder-empty-msg').removeClass('d-none');
                    $('#save-queue-order-btn').prop('disabled', true);
                } else {
                    $('#sortable-queue-list').removeClass('d-none');
                    $('#save-queue-order-btn').prop('disabled', false);

                    ccUsers.forEach((user, index) => {
                        $('#sortable-queue-list').append(`
                            <li class="list-group-item draggable-item d-flex align-items-center justify-content-between py-2.5 px-3 mb-2 rounded border" draggable="true" data-user-id="${user.id}">
                                <div class="d-flex align-items-center">
                                    <span class="drag-handle mr-3"><i class="fas fa-grip-lines"></i></span>
                                    <div>
                                        <div class="font-weight-bold text-dark fs-8">${user.name}</div>
                                        <div class="text-muted fs-9">@${user.username}</div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-primary px-2.5 py-1.5 rounded-pill font-weight-bold">
                                        No. <span class="queue-num-label">${index + 1}</span>
                                    </span>
                                </div>
                            </li>
                        `);
                    });
                }
            },
            error: function() {
                $('#reorder-loading').addClass('d-none');
                $('#reorderQueueModal').modal('hide');
                window.showToast('Gagal memuat data antrian.', 'error');
            }
        });
    });

    // Native Drag and Drop Logic
    const list = document.getElementById('sortable-queue-list');
    let draggingItem = null;

    list.addEventListener('dragstart', function(e) {
        const item = e.target.closest('.draggable-item');
        if (item) {
            draggingItem = item;
            item.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', '');
        }
    });

    list.addEventListener('dragend', function(e) {
        const item = e.target.closest('.draggable-item');
        if (item) {
            item.classList.remove('dragging');
            draggingItem = null;
            updateVisualQueueNumbers();
        }
    });

    list.addEventListener('dragover', function(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(list, e.clientY);
        const draggable = list.querySelector('.dragging');
        if (draggable) {
            if (afterElement == null) {
                list.appendChild(draggable);
            } else {
                list.insertBefore(draggable, afterElement);
            }
        }
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    function updateVisualQueueNumbers() {
        $('#sortable-queue-list .draggable-item').each(function(index) {
            $(this).find('.queue-num-label').text(index + 1);
        });
    }

    // 8. Save Queue Order Submission
    $('#save-queue-order-btn').on('click', function() {
        const orderedUserIds = [];
        $('#sortable-queue-list .draggable-item').each(function() {
            orderedUserIds.push($(this).data('user-id'));
        });

        if (orderedUserIds.length === 0) {
            window.showToast('Tidak ada data antrian untuk disimpan.', 'warning');
            return;
        }

        $.ajax({
            url: '/admin/queue/reorder',
            method: 'POST',
            data: {
                user_ids: orderedUserIds
            },
            beforeSend: function() {
                $('#save-queue-order-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Menyimpan...');
            },
            success: function(response) {
                $('#reorderQueueModal').modal('hide');
                window.showToast(response.message || 'Urutan antrian berhasil diperbarui.', 'success');
                loadUsers();
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Gagal menyimpan urutan antrian.';
                window.showToast(errorMsg, 'error');
            },
            complete: function() {
                $('#save-queue-order-btn').prop('disabled', false).text('Simpan Urutan');
            }
        });
    });
});
</script>
@endpush
