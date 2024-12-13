<!-- <div class="rating dropdown-item" data-evidence-id="{{ $evidence->id }}">
    <input type="hidden" class="rating-value" value="{{ $evidence->rating ?? 0 }}">
    @for($i = 1; $i <= 5; $i++)
        <span class="star" data-rating="{{ $i }}" style="cursor: pointer;">
            <i class="fas fa-star"></i>
        </span>
    @endfor
</div> -->


<!-- <div class="rating"> <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label> <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label> <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label> <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label> <input type="radio" name="rating" value="1" id="1"><label for="1">☆</label> </div> -->

<form  id="rating-form" data-evidence-id="{{ $evidence->id }}">
    @csrf
    <div class="rating">
        <input type="radio" name="rating" value="5" id="5" {{ $evidence->rating == 5 ? 'checked' : '' }}>
        <label for="5">☆</label>
        <input type="radio" name="rating" value="4" id="4" {{ $evidence->rating == 4 ? 'checked' : '' }}>
        <label for="4">☆</label>
        <input type="radio" name="rating" value="3" id="3" {{ $evidence->rating == 3 ? 'checked' : '' }}>
        <label for="3">☆</label>
        <input type="radio" name="rating" value="2" id="2" {{ $evidence->rating == 2 ? 'checked' : '' }}>
        <label for="2">☆</label>
        <input type="radio" name="rating" value="1" id="1" {{ $evidence->rating == 1 ? 'checked' : '' }}>
        <label for="1">☆</label>
    </div>
</form>
