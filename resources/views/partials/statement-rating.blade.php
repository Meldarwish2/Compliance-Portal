<div class="rating" data-evidence-id="{{ $evidence->id }}">
    <input type="hidden" class="rating-value" value="{{ $evidence->rating ?? 0 }}">
    @for($i = 1; $i <= 5; $i++)
        <span class="star" data-rating="{{ $i }}">
            <i class="fas fa-star"></i>
        </span>
    @endfor
</div>
