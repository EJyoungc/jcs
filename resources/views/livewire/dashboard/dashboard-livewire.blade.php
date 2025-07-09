<div>


    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>None</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">None</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid" >
        
            <!-- Default box -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <div class="form-group">
                        <input type="text" placeholder="search" class="form-control">
                    </div>
                    <div class="form-group">
                        <button @click="$wire.create(); $wire.dispatch('modal-open');"
                            class="btn-primary btn-sm"> Add <x-spinner for="create" /></button>
                    </div>

                    <x-modal title="modal" :status="$modal">
                        <form wire:submit='store'>

                            <div class="form-group">
                                <button type="submit" class="btn btn-dark">save<x-spinner for="store" /></button>
                            </div>
                        </form>
                    </x-modal>
                </div>
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-inverse ">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td scope="row"></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td scope="row"></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        </div>
        


    </section>


    


</div>
