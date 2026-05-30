<div class="row g-3">
    <label class="col-md-6">Full name<input class="form-control" name="{{ $prefix }}[full_name]" value="{{ old($prefix.'.full_name', auth()->user()->name) }}" required></label>
    <label class="col-md-6">Phone<input class="form-control" name="{{ $prefix }}[phone]" value="{{ old($prefix.'.phone', auth()->user()->phone) }}" required></label>
    <label class="col-12">Address line 1<input class="form-control" name="{{ $prefix }}[address_line_1]" value="{{ old($prefix.'.address_line_1') }}" required></label>
    <label class="col-12">Address line 2<input class="form-control" name="{{ $prefix }}[address_line_2]" value="{{ old($prefix.'.address_line_2') }}"></label>
    <label class="col-md-6">City<input class="form-control" name="{{ $prefix }}[city]" value="{{ old($prefix.'.city') }}" required></label>
    <label class="col-md-6">State<input class="form-control" name="{{ $prefix }}[state]" value="{{ old($prefix.'.state') }}"></label>
    <label class="col-md-6">Postal code<input class="form-control" name="{{ $prefix }}[postal_code]" value="{{ old($prefix.'.postal_code') }}" required></label>
    <label class="col-md-6">Country<input class="form-control" name="{{ $prefix }}[country]" value="{{ old($prefix.'.country', 'United States') }}" required></label>
</div>
