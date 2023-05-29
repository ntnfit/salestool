<!-- users.search_results.blade.php -->

@foreach ($data as $key => $user)
  <tr>
    <td>{{ ++$key }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
      @if(!empty($user->getRoleNames()))
        @foreach($user->getRoleNames() as $role)
          <label class="badge badge-success">{{ $role }}</label>
        @endforeach
      @endif
    </td>
    <td>
      <a class="btn btn-info" href="{{ route('users.show', $user->id) }}">Show</a>
      <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">Edit</a>
      {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => 'display:inline']) !!}
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
      {!! Form::close() !!}
    </td>
  </tr>
@endforeach
