@extends('layouts.app')

@section('title', 'Kelola Kebutuhan Titipan')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola Kebutuhan Titipan</h1>
        <p class="mb-0 text-gray-600 fs-7">Daftar pilihan kebutuhan untuk form titipan order.</p>
    </div>
    <button class="btn btn-primary rounded-pill px-4" data-toggle="modal" data-target="#addRequirementModal">
        <i class="fas fa-plus-circle mr-1"></i> Tambah Kebutuhan
    </button>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="requirements-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Nama Kebutuhan</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requirements-list-body">
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
                            <span>Memuat data...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addRequirementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">Tambah Kebutuhan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="add-requirement-form">
                <div class="modal-body py-3">
                    <div class="form-group mb-0">
                        <label class="text-dark font-weight-bold fs-8">Nama Kebutuhan</label>
                        <input type="text" name="name" class="form-control rounded-pill px-3" placeholder="Contoh: Perpanjangan Domain" required>
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

<!-- Edit Modal -->
<div class="modal fade" id="editRequirementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">Edit Kebutuhan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="edit-requirement-form">
                <input type="hidden" name="req_id" id="edit-req-id">
                <div class="modal-body py-3">
                    <div class="form-group mb-0">
                        <label class="text-dark font-weight-bold fs-8">Nama Kebutuhan</label>
                        <input type="text" name="name" id="edit-req-name" class="form-control rounded-pill px-3" required>
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
<div class="modal fade" id="deleteRequirementModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 44px;"></i>
                <h5 class="font-weight-bold text-dark mb-2">Hapus Kebutuhan?</h5>
                <p class="text-secondary fs-8 mb-4">Tindakan ini tidak dapat dibatalkan.</p>
                <input type="hidden" id="delete-req-id">
                <div class="d-flex justify-content-center" style="gap: 10px;">
                    <button type="button" class="btn btn-light rounded-pill px-3 btn-sm" data-dismiss="modal">Batal</button>
                    <button type="button" id="confirm-delete-req-btn" class="btn btn-danger rounded-pill px-3 btn-sm">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    loadRequirements();

    function loadRequirements() {
        $.ajax({
            url: '/admin/titipan-requirements/data',
            method: 'GET',
            success: function(data) {
                const tbody = $('#requirements-list-body');
                const reqs = data.requirements;
                if (!reqs || reqs.length === 0) {
                    tbody.html('<tr><td colspan="3" class="text-center py-5 text-muted"><i class="fas fa-tags fa-2x text-gray-300 d-block mb-3"></i>Belum ada kebutuhan.</td></tr>');
                    return;
                }
                let html = '';
                reqs.forEach((r, i) => {
                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td class="font-weight-bold text-dark">${r.name}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:6px;">
                                    <button class="btn btn-sm btn-light text-primary rounded-circle edit-req-btn" data-id="${r.id}" data-name="${r.name}" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-light text-danger rounded-circle delete-req-btn" data-id="${r.id}" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>`;
                });
                tbody.html(html);
            },
            error: function() {
                $('#requirements-list-body').html('<tr><td colspan="3" class="text-center text-danger py-4">Gagal memuat data.</td></tr>');
            }
        });
    }

    // ADD
    $('#add-requirement-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Menyimpan...');
        $(this).find('.form-control').removeClass('is-invalid');
        $.ajax({
            url: '/admin/titipan-requirements',
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                $('#addRequirementModal').modal('hide');
                $('#add-requirement-form')[0].reset();
                btn.prop('disabled', false).text('Simpan');
                loadRequirements();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Kebutuhan berhasil ditambahkan!', timer: 1500, showConfirmButton: false });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(k => {
                        const input = $('#add-requirement-form').find(`[name="${k}"]`);
                        input.addClass('is-invalid').siblings('.invalid-feedback').text(errors[k][0]);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.' });
                }
            }
        });
    });

    // OPEN EDIT
    $(document).on('click', '.edit-req-btn', function() {
        $('#edit-req-id').val($(this).data('id'));
        $('#edit-req-name').val($(this).data('name'));
        $('#edit-requirement-form .form-control').removeClass('is-invalid');
        $('#editRequirementModal').modal('show');
    });

    // UPDATE
    $('#edit-requirement-form').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-req-id').val();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Menyimpan...');
        $(this).find('.form-control').removeClass('is-invalid');
        $.ajax({
            url: `/admin/titipan-requirements/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            success: function() {
                $('#editRequirementModal').modal('hide');
                btn.prop('disabled', false).text('Simpan');
                loadRequirements();
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Kebutuhan berhasil diperbarui!', timer: 1500, showConfirmButton: false });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(k => {
                        const input = $('#edit-requirement-form').find(`[name="${k}"]`);
                        input.addClass('is-invalid').siblings('.invalid-feedback').text(errors[k][0]);
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.' });
                }
            }
        });
    });

    // OPEN DELETE
    $(document).on('click', '.delete-req-btn', function() {
        $('#delete-req-id').val($(this).data('id'));
        $('#deleteRequirementModal').modal('show');
    });

    // CONFIRM DELETE
    $('#confirm-delete-req-btn').on('click', function() {
        const id = $('#delete-req-id').val();
        const btn = $(this);
        btn.prop('disabled', true).text('Menghapus...');
        $.ajax({
            url: `/admin/titipan-requirements/${id}`,
            method: 'DELETE',
            success: function() {
                $('#deleteRequirementModal').modal('hide');
                btn.prop('disabled', false).text('Hapus');
                loadRequirements();
                Swal.fire({ icon: 'success', title: 'Terhapus', text: 'Kebutuhan berhasil dihapus.', timer: 1400, showConfirmButton: false });
            },
            error: function() {
                btn.prop('disabled', false).text('Hapus');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghapus kebutuhan.' });
            }
        });
    });
});
</script>
@endpush
