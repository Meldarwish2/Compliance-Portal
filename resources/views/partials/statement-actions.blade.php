<div class="dropdown d-inline-block">
    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ri-more-fill align-middle"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @role('client')
        <li>
            <form id="upload-evidence-form-{{ $statement->id }}" class="mt-3" enctype="multipart/form-data">
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
        </li>
        @endrole

        @role('auditor')
        @if($project->type == 'accept_reject')
            @foreach($statement->evidences as $evidence)
                <li>
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="dropdown-item">
                        <i class="ri-download-2-fill align-bottom me-2 text-muted"></i> Download Evidence
                    </a>
                </li>
                <li>
                    <form action="{{ route('evidences.approve', $evidence->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ri-check-line align-bottom me-2 text-muted"></i> Approve
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('evidences.reject', $evidence->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ri-close-line align-bottom me-2 text-muted"></i> Reject
                        </button>
                    </form>
                </li>
            @endforeach
        @elseif($project->type == 'rating')
            @foreach($statement->evidences as $evidence)
                <li>
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="dropdown-item">
                        <i class="ri-download-2-fill align-bottom me-2 text-muted"></i> Download Evidence
                    </a>
                </li>
                <li>
                
                  @include('partials.statement-rating')
                 
                </li>
                <li>
                    <form action="{{ route('evidences.reject', $evidence->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="ri-close-line align-bottom me-2 text-muted"></i> Reject
                        </button>
                    </form>
                </li>
            @endforeach
        @elseif($project->type == 'compliance')
            @foreach($statement->evidences as $evidence)
                <li>
                    <a href="{{ route('evidences.download', $evidence->id) }}" class="dropdown-item">
                        <i class="ri-download-2-fill align-bottom me-2 text-muted"></i> Download Evidence
                    </a>
                </li>
                <li>
                    <div class="compliance-buttons">
                        <form action="{{ route('evidences.compliance', $evidence->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="compliance" value="compliant">
                            <button type="submit" class="dropdown-item">
                                <i class="ri-check-line align-bottom me-2 text-muted"></i> Compliant
                            </button>
                        </form>
                        <form action="{{ route('evidences.compliance', $evidence->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="compliance" value="partially_compliant">
                            <button type="submit" class="dropdown-item">
                                <i class="ri-question-line align-bottom me-2 text-muted"></i> Partially Compliant
                            </button>
                        </form>
                        <form action="{{ route('evidences.compliance', $evidence->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="compliance" value="rejected">
                            <button type="submit" class="dropdown-item">
                                <i class="ri-close-line align-bottom me-2 text-muted"></i> Rejected
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        @endif
        @endrole

        <li>
            <button type="button" class="dropdown-item add-comment" data-bs-toggle="modal" data-bs-target="#addCommentModal{{ $statement->id }}">
                <i class="ri-chat-1-fill align-bottom me-2 text-muted"></i> Add Comment
            </button>
        </li>
    </ul>
</div>
