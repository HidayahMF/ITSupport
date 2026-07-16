<!DOCTYPE html>
<html lang="en">

    <head>
        <!-- META -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- TAILWINDCSS -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

        <!-- DAISYUI -->
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" />

        <!-- SELECT2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

        <!-- DATATABLES -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

        <!-- JQUERY -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- SELECT2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- WEBCAM -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

        <!-- DATATABLES JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <!-- SWEETALERT -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>





        <style>
            .select2-container .select2-selection--single {
                height: 48px !important;
                padding: 8px 12px !important;
                border-radius: 12px !important;
                border: 1px solid #d1d5db !important;
                background-color: #ffffff !important;
                transition: all 0.15s ease-in-out;
                margin-bottom: 20px;
            }

            .select2-selection__rendered {
                line-height: 32px !important;
                color: #374151 !important;
            }

            .select2-selection__arrow {
                top: 10px !important;
                right: 10px !important;
            }

            .select2-container--default .select2-selection--single:focus,
            .select2-container--default.select2-container--open .select2-selection--single {
                outline: none !important;
                border-color: #facc15 !important;
                box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.5) !important;
            }

            /* WRAPPER */
            .dt-container {
                width: 100%;
            }

            /* SEARCH BAR */
            .dataTables_filter {
                float: right !important;
                margin-bottom: 5px;
                padding: 10px;
            }

            .dataTables_filter label {
                font-size: 14px;
                color: #475569;
                font-weight: 600;
            }

            .dataTables_filter input {
                margin-left: 8px;
                padding: 8px 14px;
                width: 230px;
                border-radius: 12px;
                border: 1px solid #cbd5e1;
                outline: none;
                transition: .2s;
            }

            .dataTables_filter input:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
            }

            /* TABLE */
            #latestTable {
                border-collapse: separate !important;
                border-spacing: 0 !important;
                width: 100%;
                overflow: hidden;
            }

            #latestTable thead tr {
                background-color: #f1f5f9;
                /* slate-100 */
            }

            #latestTable thead th {
                padding: 14px 16px !important;
                color: #334155;
                font-size: 14px;
                font-weight: 700;
                border-bottom: 1px solid #e2e8f0;
            }

            #latestTable tbody td {
                padding: 16px !important;
                font-size: 14px;
                color: #475569;
                border-bottom: 1px solid #f1f5f9;
            }

            #latestTable tbody tr:hover {
                background-color: #f8fafc !important;
            }

            /* PAGINATION */
            .dataTables_paginate {
                margin-top: 5px !important;
                text-align: right !important;
            }

            .dataTables_paginate a {
                padding: 6px 10px;
                margin: 2px;
                border-radius: 8px;
                background: white;
                color: #475569 !important;
                cursor: pointer;
                transition: .2s;
                border: 1px solid #e2e8f0;
            }

            .dataTables_paginate a:hover {
                background: #e2e8f0;
            }

            .dataTables_paginate .current {
                background: #3b82f6 !important;
                color: white !important;
                border-color: #3b82f6 !important;
            }

            /* HIDE INFO */
            .dataTables_info {
                display: block !important;
                font-size: 13px;
                color: #64748b;
                margin-top: 8px;
            }

            /* LENGTH SELECT (DROP) */
            .dataTables_length {
                display: none;
            }
        </style>

    </head>


<body>