<div class="modal fade" id="addCommentModal{{ $statement->id }}" tabindex="-1" aria-labelledby="addCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCommentModalLabel">Add Comment for Statement {{ $statement->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('comments.store', $statement->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="comment">Your Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>
