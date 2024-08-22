@if ($user->bannerDefault)
    <div class="mw-100 mb-2 user-banner" style="background-image: url('{{ $user->banner ? $user->bannerUrl : asset($user->bannerDefault) }}'); {{ $user->bannerStyling }}"></div>
@endif
