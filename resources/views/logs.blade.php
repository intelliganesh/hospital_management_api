@extends('app.index')
@section('style')
    <style>
        .logs-container {
            padding: 20px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        table td,
        table th {
            padding: 8px;
            text-align: left;
            border: 1px solid var(--secondary-bg-color);
        }

        table tr:nth-child(even) {
            background-color: var(--primary-bg-color);
        }

        table tr:hover {
            background-color: var(--secondary-bg-color);
        }

        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: var(--primary-color);
            color: var(--white-color);
        }

        .flex-container {
            display: flex;
            align-items: center;
            margin: 0px 0px 10px 0px;
            justify-content: space-between;
        }

        .table-header tr {
            top: 0px;
            position: sticky;
            position: -webkit-sticky;
            box-shadow: 0px 0px 15px lightgray;
        }

        .main-table {
            height: 70vh;
            overflow: auto;
            min-height: 50px;
        }
    </style>
@endsection
@section('content')
    <div class="logs-container">
        <h1 class="heading heading-in-logs">Hospital Management Logs</h1>
        <div class="flex-container">
            <h2 class="small-heading">Total Logs ({{ $logs->total() }})</h2>
            <a class="button" href="{{ url('/') }}">Back</a>
        </div>
        <div class="table-responsive main-table">
            <table>
                <thead class="table-header">
                    <tr>
                        <th class="white-space-nowrap">Sl No</th>
                        <th class="white-space-nowrap">Subject</th>
                        <th class="white-space-nowrap">Log</th>
                        <th class="white-space-nowrap">Status type</th>
                        <th class="white-space-nowrap">Status code</th>
                        <th class="white-space-nowrap">Url</th>
                        <th class="white-space-nowrap">Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->subject }}</td>
                            <td style="cursor: pointer" onclick="onModel(this)" id="myBtn_{{ $index }}"
                                data-modelid="{{ $index }}" data-content="{{ $item->log }}">
                                {{ substr($item->log, 30, 30) . '...' }}
                            </td>
                            <td>{{ $item->status_type }}</td>
                            <td>{{ $item->status_code }}</td>
                            <td>{{ $item->url }}</td>
                            <td>{{ $item->method }}</td>
                            @include('model', ['index' => $index])
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if (count($logs) > 0)
        <div class="pagination_links">
            {{ $logs->links('pagination.default') }}
        </div>
    @endif
@endsection
@section('script')
    <script>
        function onModel(current) {
            const modalId = current.getAttribute('data-modelid');
            const content = current.getAttribute('data-content');
            const modal = document.getElementById(`myModal_${modalId}`);
            const btn = document.getElementById(`myBtn_${modalId}`);
            modal.style.display = "block";
            const subject = document.getElementById(`subject_${modalId}`);
            subject.textContent = content;
        }

        function closeModal(current) {
            const modalId = current.getAttribute('data-modelid');
            const modal = document.getElementById(`myModal_${modalId}`);
            modal.style.display = "none";
        }
    </script>
@endsection
