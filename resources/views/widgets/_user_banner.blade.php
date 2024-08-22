@if ($user->banner)
    <div class="mw-100 mb-2" style="background-image: url('{{ $user->bannerUrl }}'); {{ $user->bannerStyling }} min-height: 150px;"></div>
@endif
