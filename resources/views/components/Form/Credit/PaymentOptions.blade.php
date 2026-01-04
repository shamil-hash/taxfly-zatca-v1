<select name="{{ $selectName }}" id="{{ $selectId }}" class="{{ $selectClass }}" style="width:80%;">
    <option value="">{{ $defaultOptionText ?? 'Select Payment Option' }}</option>
    <option value="1">CASH</option>
    <option value="2">CHEQUE</option>
    <option value="3">BANK</option>

</select>
