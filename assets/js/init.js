(function ($) {
    "use strict";
    $(function () {
        $(document).ready(function () {

            $('.datetimepicker').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                autoclose: true,
                language: 'pt-BR'

            });

        });
    });

}(jQuery));