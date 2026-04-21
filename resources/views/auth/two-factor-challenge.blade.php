@extends('layouts.app')

@section('title')
    Login: Two-Factor Auth
@endsection

@section('content')
    <div class="text-center">
        <h1>Two-Factor Authentication</h1>
    </div>

    {{ html()->form('POST', url('two-factor-challenge'))->open() }}
    <div class="form-group row">
        {{ html()->label('Code', 'code')->class('col-md-3 col-form-label text-md-right') }}
        <div class="col-md-7">
            {{ html()->text('code')->class('form-control') }}
        </div>
    </div>

    <div class="form-group row">
        {{ html()->label('Use a Recovery Code', 'use_recovery')->class('form-label text-md-right col-md-6') }}
        <div class="col-md-6">
            {{ html()->checkbox('use_recovery', old('use_recovery'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle')->attribute('data-on', 'Yes')->attribute('data-off', 'No')->id('useRecovery') }}
        </div>
    </div>
    <div class="mb-3" id="recoveryContainer">
        <div class="form-group row">
            {{ html()->label('Recovery Code', 'recovery_code')->class('col-md-3 col-form-label text-md-right') }}
            <div class="col-md-7">
                {{ html()->text('recovery_code')->class('form-control') }}
            </div>
        </div>
    </div>

    <div class="text-right">
        {{ html()->submit('Submit')->class('btn btn-primary') }}
    </div>
    {{ html()->form()->close() }}
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function() {
            var $useRecovery = $('#useRecovery');
            var $recoveryContainer = $('#recoveryContainer');

            var useRecovery = $useRecovery.is(':checked');

            updateOptions();

            $useRecovery.on('change', function(e) {
                useRecovery = $useRecovery.is(':checked');

                updateOptions();
            });

            function updateOptions() {
                if (useRecovery) $recoveryContainer.removeClass('hide');
                else $recoveryContainer.addClass('hide');
            }
        });
    </script>
@endsection
