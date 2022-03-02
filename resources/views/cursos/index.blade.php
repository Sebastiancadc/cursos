@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Historial</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Cursos realziados</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            @foreach ($cursos as $curso)
                          <tr>
                            <th scope="row">{{$i++}}</th>
                            <td>{{$curso->nombre_usuario}}</td>
                            <td>{{$curso->total}}</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
