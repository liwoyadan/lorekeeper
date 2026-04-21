<h3>Coupon</h3>

<h4>Discount</h4>
<p>Input discount percent. You can select what coupons can be used in each shop on the shop edit page.</p>

<div class="row">
    <div class="col-md-6 form-group">
        {{ html()->label('Discount', 'discount') }}
        {{ html()->number('discount', $tag->getData()['discount'])->class('form-control')->placeholder('Input Discount Percent')->attribute('min', 1)->attribute('max', 100) }}
    </div>
    <div class="col-md-6 form-group pt-4">
        {{ html()->checkbox('infinite', $tag->getData()['infinite'], 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Should this coupon be unlimited use?', 'infinite')->class('ml-3 form-check-label') }}
    </div>
</div>
