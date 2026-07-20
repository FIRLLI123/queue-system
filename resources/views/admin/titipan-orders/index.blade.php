@extends('layouts.app')

@section('title', 'Kelola Titipan Order')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola Titipan Order</h1>
        <p class="mb-0 text-gray-600 fs-7">Manajemen titipan booking order/antrian Customer Care.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-toggle="modal" data-target="#addTitipanModal">
        <i class="fas fa-plus-circle mr-1"></i> Tambah Titipan Order
    </button>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="titipan-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Tanggal Booking</th>
                        <th>Jam Booking</th>
                        <th>Kebutuhan</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Diambil Oleh</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="titipan-list-body">
                    <!-- Loaded dynamically via AJAX -->
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
                            <span>Memuat data titipan order...</span>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- Add Titipan Modal -->
<div class="modal fade" id="addTitipanModal" tabindex="-1" role="dialog" aria-labelledby="addTitipanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="addTitipanModalLabel">Tambah Titipan Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-titipan-form">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Tanggal Booking</label>
                        <input type="date" name="booking_date" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Jam Booking</label>
                        <input type="time" name="booking_time" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Kebutuhan</label>
                        <select name="requirement" id="requirement-select" class="form-control rounded-pill px-3" style="height: 40px;" required>
                            <option value="" disabled selected>Memuat pilihan...</option>
                        </select>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Deskripsi</label>
                        <textarea name="description" class="form-control rounded-4 px-3 py-2" rows="3" placeholder="Masukkan deskripsi detail titipan..."></textarea>
                        <div class="invalid-feedback px-2"></div>
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

<!-- Edit Titipan Modal -->
<div class="modal fade" id="editTitipanModal" tabindex="-1" role="dialog" aria-labelledby="editTitipanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="editTitipanModalLabel">Edit Titipan Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-titipan-form">
                <input type="hidden" name="titipan_id" id="edit-titipan-id">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Tanggal Booking</label>
                        <input type="date" name="booking_date" id="edit-titipan-date" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Jam Booking</label>
                        <input type="time" name="booking_time" id="edit-titipan-time" class="form-control rounded-pill px-3" required>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Kebutuhan</label>
                        <select name="requirement" id="edit-titipan-requirement" class="form-control rounded-pill px-3" style="height: 40px;" required>
                            <option value="Pilihan 1">Pilihan 1</option>
                            <option value="Pilihan 2">Pilihan 2</option>
                        </select>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Deskripsi</label>
                        <textarea name="description" id="edit-titipan-description" class="form-control rounded-4 px-3 py-2" rows="3"></textarea>
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Status</label>
                        <select name="status" id="edit-titipan-status" class="form-control rounded-pill px-3" style="height: 40px;" required>
                            <option value="CREATE">CREATE</option>
                            <option value="COMPLETED">COMPLETED</option>
                        </select>
                        <div class="invalid-feedback px-2"></div>
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

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteTitipanModal" tabindex="-1" role="dialog" aria-labelledby="deleteTitipanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 48px;"></i>
                <h5 class="font-weight-bold text-dark mb-2">Hapus Titipan Order?</h5>
                <p class="text-secondary fs-8 mb-4">Apakah Anda yakin ingin menghapus data titipan order ini?</p>
                <input type="hidden" id="delete-titipan-id">
                <div class="d-flex justify-content-center gap-2" style="gap: 10px;">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" id="confirm-delete-titipan-btn" class="btn btn-danger rounded-pill px-3 btn-sm">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const currentUserId = {{ auth()->user()->id }};
    const userRole = '{{ auth()->user()->role }}';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    loadTitipanOrders();

    // Load requirement options from DB
    function loadRequirementOptions(selectId) {
        const sel = $(selectId);
        sel.html('<option value="" disabled selected>Memuat pilihan...</option>');
        $.ajax({
            url: '/titipan-requirements',
            method: 'GET',
            success: function(data) {
                const opts = (data.requirements || []);
                if (opts.length === 0) {
                    sel.html('<option value="" disabled selected>Belum ada pilihan</option>');
                    return;
                }
                let html = '<option value="" disabled selected>Pilih Kebutuhan</option>';
                opts.forEach(r => { html += `<option value="${r.name}">${r.name}</option>`; });
                sel.html(html);
            },
            error: function() {
                sel.html('<option value="" disabled selected>Gagal memuat</option>');
            }
        });
    }

    // Load when modal opens
    $('#addTitipanModal').on('show.bs.modal', function() {
        loadRequirementOptions('#requirement-select');
    });

    function loadTitipanOrders() {
        const tbody = $('#titipan-list-body');
        $.ajax({
            url: '/titipan-orders/data',
            method: 'GET',
            success: function(response) {
                const orders = response.titipan_orders;
                if (!orders || orders.length === 0) {
                    tbody.html(`
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-3 text-gray-300 d-block"></i>
                                Belum ada data titipan order.
                            </td>
                        </tr>
                    `);
                    return;
                }

                let html = '';
                orders.forEach((o, index) => {
                    // format date
                    const d = new Date(o.booking_date);
                    const formattedDate = d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    const formattedTime = o.booking_time.substring(0, 5);

                    // badge status
                    const statusBadge = o.status === 'CREATE'
                        ? '<span class="badge badge-pill bg-light-primary text-primary px-3 py-2 font-weight-bold">CREATE</span>'
                        : '<span class="badge badge-pill bg-light-success text-success px-3 py-2 font-weight-bold">COMPLETED</span>';

                    // taken by
                    let takenInfo = '–';
                    if (o.status === 'COMPLETED' && o.taken_by) {
                        const takenTime = o.taken_at ? new Date(o.taken_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                        takenInfo = `
                            <div>
                                <span class="font-weight-bold text-dark">${o.taken_by.name}</span>
                                <div class="text-muted fs-8">Pukul ${takenTime}</div>
                            </div>
                        `;
                    }

                    // description fallback
                    const desc = o.description ? o.description : '<span class="text-muted italic">–</span>';

                    // action buttons
                    let actionBtn = '<span class="text-muted">–</span>';
                    const canDelete = o.status !== 'COMPLETED' && (userRole === 'ADMIN' || parseInt(o.created_by_user_id) === currentUserId);
                    if (canDelete) {
                        actionBtn = `
                            <div class="d-flex justify-content-center gap-2" style="gap: 6px;">
                                <button class="btn btn-sm btn-light text-danger rounded-circle delete-btn" data-id="${o.id}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }


                    // creator
                    const creatorName = o.creator ? o.creator.name : '<span class="text-muted italic">–</span>';

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td class="font-weight-bold text-dark">${formattedDate}</td>
                            <td><span class="badge bg-light text-dark px-2 py-1.5 border font-monospace" style="font-size:12px;"><i class="far fa-clock mr-1 text-primary"></i>${formattedTime}</span></td>
                            <td><span class="badge bg-light text-purple px-2 py-1.5 border font-weight-bold">${o.requirement}</span></td>
                            <td><div class="text-truncate" style="max-width: 250px;" title="${o.description || ''}">${desc}</div></td>
                            <td>${statusBadge}</td>
                            <td><span class="font-weight-bold text-gray-800">${creatorName}</span></td>
                            <td>${takenInfo}</td>
                            <td class="text-center">${actionBtn}</td>
                        </tr>
                    `;
                });
                tbody.html(html);
            },
            error: function() {
                tbody.html(`
                    <tr>
                        <td colspan="9" class="text-center py-5 text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                            Gagal memuat data titipan order. Silakan coba lagi.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // CREATE
    $('#add-titipan-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status"></span>Menyimpan...');
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: '/titipan-orders',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#addTitipanModal').modal('hide');
                form[0].reset();
                submitBtn.prop('disabled', false).html('Simpan');
                loadTitipanOrders();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Titipan order berhasil ditambahkan!',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('Simpan');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        const input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan. Silakan coba lagi.'
                    });
                }
            }
        });
    });

    // OPEN EDIT MODAL
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const form = $('#edit-titipan-form');
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: `/titipan-orders/${id}`,
            method: 'GET',
            success: function(response) {
                const o = response.titipan_order;
                $('#edit-titipan-id').val(o.id);
                // format date to YYYY-MM-DD
                const dateVal = o.booking_date ? o.booking_date.substring(0, 10) : '';
                $('#edit-titipan-date').val(dateVal);
                $('#edit-titipan-time').val(o.booking_time.substring(0, 5));
                $('#edit-titipan-requirement').val(o.requirement);
                $('#edit-titipan-description').val(o.description);
                $('#edit-titipan-status').val(o.status);
                $('#editTitipanModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mengambil data titipan order.'
                });
            }
        });
    });

    // UPDATE
    $('#edit-titipan-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const id = $('#edit-titipan-id').val();
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-2" role="status"></span>Menyimpan...');
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: `/titipan-orders/${id}`,
            method: 'PUT',
            data: form.serialize(),
            success: function(response) {
                $('#editTitipanModal').modal('hide');
                submitBtn.prop('disabled', false).html('Simpan');
                loadTitipanOrders();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Titipan order berhasil diperbarui!',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html('Simpan');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        const input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan. Silakan coba lagi.'
                    });
                }
            }
        });
    });

    // OPEN DELETE MODAL
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        $('#delete-titipan-id').val(id);
        $('#deleteTitipanModal').modal('show');
    });

    // CONFIRM DELETE
    $('#confirm-delete-titipan-btn').on('click', function() {
        const id = $('#delete-titipan-id').val();
        const btn = $(this);
        btn.prop('disabled', true).text('Menghapus...');

        $.ajax({
            url: `/titipan-orders/${id}`,
            method: 'DELETE',
            success: function(response) {
                $('#deleteTitipanModal').modal('hide');
                btn.prop('disabled', false).text('Hapus');
                loadTitipanOrders();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Titipan order berhasil dihapus!',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function() {
                btn.prop('disabled', false).text('Hapus');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghapus data titipan order.'
                });
            }
        });
    });
});
</script>
@endpush

<style>
.bg-light-primary {
    background-color: rgba(59, 130, 246, 0.12) !important;
}
.bg-light-success {
    background-color: rgba(16, 185, 129, 0.12) !important;
}
.text-purple {
    color: #7c3aed !important;
}
.bg-light.text-purple {
    background-color: rgba(139, 92, 246, 0.12) !important;
}
</style>
