<form id="upload-evidence-form-{{ $statement->id }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="statement_id" value="{{ $statement->id }}">
    <div class="form-group">
        <input type="file" name="evidence" class="form-control" required>
    </div>
    <button type="button" class="dropdown-item submit-evidence" data-statement-id="{{ $statement->id }}">
        <i class="ri-upload-2-fill align-bottom me-2 text-muted"></i> Upload Evidence
    </button>
    <div id="evidence-message-{{ $statement->id }}" class="mt-2"></div>
</form>
