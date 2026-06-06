@extends('layouts.admin')

@section('title', 'User Management - Admin')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <p class="text-gray-600 mt-1">Kelola user dan status akun</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terakhir Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $user->username }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->branch->city ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->level === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->level === 'admin' ? 'Admin' : 'User' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline toggle-form" data-user-name="{{ $user->name }}" data-user-status="{{ $user->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="toggle-btn px-3 py-1 text-sm rounded {{ $user->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} transition">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

@include('components.modal-confirm')

<script>
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.toggle-form');
            const userName = form.dataset.userName;
            const action = form.dataset.userStatus;
            const actionText = action === 'aktifkan' ? 'Mengaktifkan' : 'Menonaktifkan';
            
            showConfirmModal(
                'Konfirmasi ' + (action === 'aktifkan' ? 'Aktivasi' : 'Nonaktifkan'),
                `Apakah Anda yakin ingin ${action} user "${userName}"?`,
                () => form.submit()
            );
        });
    });
</script>
@endsection