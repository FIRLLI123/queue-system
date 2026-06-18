@extends('layouts.app')

@section('title', 'Kelola Tipe Order')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola Tipe Order</h1>
        <p class="mb-0 text-gray-600 fs-7">Manajemen tipe/kategori order antrian Customer Care.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-toggle="modal" data-target="#addOrderTypeModal">
        <i class="fas fa-plus-circle mr-1"></i> Tambah Tipe Order
    </button>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="order-types-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Ikon / Logo</th>
                        <th>Nama Tipe Order</th>
                        <th>Status</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="order-types-list-body">
                    <!-- Loaded dynamically via AJAX -->
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
                            <span>Memuat data tipe order...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Order Type Modal -->
<div class="modal fade" id="addOrderTypeModal" tabindex="-1" role="dialog" aria-labelledby="addOrderTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="addOrderTypeModalLabel">Tambah Tipe Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add-order-type-form">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Nama Tipe Order</label>
                        <input type="text" name="name" class="form-control rounded-pill px-3" required placeholder="Contoh: CMS, CRM, OTHER" style="text-transform: uppercase;">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Status</label>
                        <select name="status" class="form-control rounded-pill px-3" style="height: 40px;" required>
                            <option value="ACTIVE" selected>ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
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

<!-- Edit Order Type Modal -->
<div class="modal fade" id="editOrderTypeModal" tabindex="-1" role="dialog" aria-labelledby="editOrderTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="editOrderTypeModalLabel">Edit Tipe Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-order-type-form">
                <input type="hidden" name="order_type_id" id="edit-order-type-id">
                <div class="modal-body py-3">
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Nama Tipe Order</label>
                        <input type="text" name="name" id="edit-order-type-name" class="form-control rounded-pill px-3" required style="text-transform: uppercase;">
                        <div class="invalid-feedback px-2"></div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-dark font-weight-bold fs-8">Status</label>
                        <select name="status" id="edit-order-type-status" class="form-control rounded-pill px-3" style="height: 40px;" required>
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="INACTIVE">INACTIVE</option>
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
<div class="modal fade" id="deleteOrderTypeModal" tabindex="-1" role="dialog" aria-labelledby="deleteOrderTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 48px;"></i>
                <h5 class="font-weight-bold text-dark mb-2">Hapus Tipe Order?</h5>
                <p class="text-secondary fs-8 mb-4">Tipe order yang memiliki riwayat transaksi tidak dapat dihapus, tapi statusnya bisa dinonaktifkan.</p>
                <input type="hidden" id="delete-order-type-id">
                <div class="d-flex justify-content-center gap-2" style="gap: 10px;">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" id="confirm-delete-type-btn" class="btn btn-danger rounded-pill px-3 btn-sm">Hapus</button>
                </div>
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

    // Helper to get matching logo for order types
    function getOrderTypeIcon(name) {
        if (name === 'CMS') {
            return `<span class="badge bg-light text-success p-2.5 rounded-circle border"><i class="fab fa-whatsapp fa-lg"></i></span>`;
        } else if (name === 'CRM') {
            return `<span class="badge bg-light text-success p-2.5 rounded-circle border"><i class="fas fa-phone fa-lg"></i></span>`;
        } else if (name === 'OTHER') {
            return `<span class="badge bg-light text-purple p-2.5 rounded-circle border"><i class="fas fa-cog fa-lg"></i></span>`;
        }
        return `<span class="badge bg-light text-secondary p-2.5 rounded-circle border"><i class="fas fa-tag fa-lg"></i></span>`;
    }

    // 1. Load Order Types List
    function loadOrderTypes() {
        $.ajax({
            url: '/admin/order-types/data',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const tbody = $('#order-types-list-body');
                tbody.empty();

                if (data.order_types && data.order_types.length > 0) {
                    data.order_types.forEach((type, index) => {
                        const statusBadge = type.status === 'ACTIVE'
                            ? `<span class="badge badge-success px-2.5 py-1.5 rounded-pill font-weight-bold"><i class="fas fa-circle mr-1 fs-9 text-white pulse-green"></i>ACTIVE</span>`
                            : `<span class="badge badge-secondary px-2.5 py-1.5 rounded-pill font-weight-bold">INACTIVE</span>`;
                        
                        tbody.append(`
                            <tr>
                                <td class="font-monospace">${index + 1}</td>
                                <td>${getOrderTypeIcon(type.name)}</td>
                                <td>
                                    <strong class="text-dark font-monospace">${type.name}</strong>
                                </td>
                                <td>${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn btn-light btn-sm rounded-circle mr-1 edit-order-type-btn" data-id="${type.id}" title="Edit Tipe Order">
                                        <i class="fas fa-edit text-primary"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm rounded-circle delete-order-type-btn" data-id="${type.id}" title="Hapus Tipe Order">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    tbody.html(`
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">
                                Tidak ada data tipe order ditemukan.
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                window.showToast('Gagal memuat data tipe order.', 'error');
            }
        });
    }

    // Initial Load
    loadOrderTypes();

    // Reset error styling on modal close
    $('.modal').on('hidden.bs.modal', function() {
        const form = $(this).find('form');
        if (form.length) {
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').empty();
        }
    });

    // Force uppercase text in order type name
    $('input[name="name"]').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // 2. Create Order Type Submission
    $('#add-order-type-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').empty();

        $.ajax({
            url: '/admin/order-types',
            method: 'POST',
            data: form.serialize(),
            success: function(data) {
                $('#addOrderTypeModal').modal('hide');
                window.showToast('Tipe order baru berhasil ditambahkan.', 'success');
                loadOrderTypes();
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
                    window.showToast('Terjadi kesalahan saat menyimpan tipe order.', 'error');
                }
            }
        });
    });

    // 3. Edit Order Type (Open Modal & Populate Form)
    $(document).on('click', '.edit-order-type-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `/admin/order-types/${id}`,
            method: 'GET',
            success: function(data) {
                const type = data.order_type;
                $('#edit-order-type-id').val(type.id);
                $('#edit-order-type-name').val(type.name);
                $('#edit-order-type-status').val(type.status);
                
                $('#editOrderTypeModal').modal('show');
            },
            error: function() {
                window.showToast('Gagal memuat detail tipe order.', 'error');
            }
        });
    });

    // 4. Update Order Type Submission
    $('#edit-order-type-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const id = $('#edit-order-type-id').val();
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').empty();

        $.ajax({
            url: `/admin/order-types/${id}`,
            method: 'PUT',
            data: form.serialize(),
            success: function(data) {
                $('#editOrderTypeModal').modal('hide');
                window.showToast('Tipe order berhasil diperbarui.', 'success');
                loadOrderTypes();
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
                    window.showToast('Terjadi kesalahan saat memperbarui tipe order.', 'error');
                }
            }
        });
    });

    // 5. Delete Order Type Click
    $(document).on('click', '.delete-order-type-btn', function() {
        const id = $(this).data('id');
        $('#delete-order-type-id').val(id);
        $('#deleteOrderTypeModal').modal('show');
    });

    // 6. Delete Order Type Confirmation
    $('#confirm-delete-type-btn').on('click', function() {
        const id = $('#delete-order-type-id').val();
        $.ajax({
            url: `/admin/order-types/${id}`,
            method: 'DELETE',
            success: function(data) {
                $('#deleteOrderTypeModal').modal('hide');
                window.showToast('Tipe order berhasil dihapus.', 'success');
                loadOrderTypes();
            },
            error: function(xhr) {
                $('#deleteOrderTypeModal').modal('hide');
                const errMsg = xhr.responseJSON && xhr.responseJSON.message 
                    ? xhr.responseJSON.message 
                    : 'Gagal menghapus tipe order.';
                window.showToast(errMsg, 'error');
            }
        });
    });
});
</script>
@endpush
