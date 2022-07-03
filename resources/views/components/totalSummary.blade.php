<div class="card">
    <div class="card-header">
        <h3 class="d-block w-100 text-center">Total Amount Breakdown</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Transaction Count:</th>
                                {{$count}}
                            </tr>
                            <tr>
                                <th>Total Pending</th>
                                {{$pending}}
                            </tr>
                            <tr>
                                <th>Total Failed:</th>
                                {{$failed}}
                            </tr>
                            <tr>
                                <th>Total Confirmed:</th>
                                {{$confirmed}}
                            </tr>
                            <tr>
                                <th>Total:</th>
                                {{$total}}
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>