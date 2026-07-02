@extends('Superadmin.layouts.master')

@section('title', 'Administrator - Control Deck')

@section('content')
    <div style="max-width: 1600px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px; color: white; box-shadow: 0 12px 30px rgba(15, 23, 42, 0.16);">
            <div>
                <h2 style="margin: 0; font-size: 1.6rem; font-weight: 700;">Administrator Control Center</h2>
                <p style="margin: 0.3rem 0 0; color: #cbd5e1;">Create and manage enterprise admin accounts with secure credentials.</p>
            </div>
            <div style="padding: 0.7rem 1rem; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); border-radius: 999px; font-weight: 600;">
                Super Admin Workspace
            </div>
        </div>

        @if(session('success'))
            <div style="padding: 1rem 1.2rem; border-radius: 12px; background: #ecfdf3; color: #166534; border: 1px solid #a7f3d0; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="padding: 1rem 1.2rem; border-radius: 12px; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 1.5rem; align-items: start;">
            <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Create New Admin</h3>
                        <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.95rem;">Set up a new administrator account with secure login access.</p>
                    </div>
                    <div style="padding: 0.45rem 0.8rem; border-radius: 999px; background: #eff6ff; color: #2563eb; font-weight: 700; font-size: 0.8rem;">ERP Access</div>
                </div>

                <form action="{{ route('superadmin.Administrator.store') }}" method="POST">
                    @csrf
                    <div style="display: grid; gap: 1rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; color: #334155;">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.8rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; color: #334155;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.8rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; color: #334155;">Password</label>
                            <input type="password" name="password" required style="width: 100%; padding: 0.8rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                            <p style="margin: 0.4rem 0 0; color: #64748b; font-size: 0.85rem;">Use 8–16 characters with uppercase, lowercase, a number, and a special character.</p>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.35rem; font-weight: 600; color: #334155;">Confirm Password</label>
                            <input type="password" name="password_confirmation" required style="width: 100%; padding: 0.8rem 0.9rem; border: 1px solid #cbd5e1; border-radius: 10px; background: #f8fafc;">
                        </div>

                        <button type="submit" style="width: fit-content; padding: 0.8rem 1.2rem; border: none; border-radius: 10px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white; font-weight: 700; cursor: pointer;">Create Admin Account</button>
                    </div>
                </form>
            </div>

            <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700;">Admin Accounts</h3>
                        <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.95rem;">Authorized Admin for the VIA Architects Payroll</p>
                    </div>
                    <div style="padding: 0.45rem 0.8rem; border-radius: 999px; background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem;">{{ $admins->count() }} Total</div>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0; text-align: left; color: #64748b; font-size: 0.9rem;">
                            <th style="padding: 0.75rem 0.5rem;">Name</th>
                            <th style="padding: 0.75rem 0.5rem;">Email</th>
                            <th style="padding: 0.75rem 0.5rem;">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.8rem 0.5rem; font-weight: 600; color: #0f172a;">{{ $admin->name }}</td>
                                <td style="padding: 0.8rem 0.5rem; color: #475569;">{{ $admin->email }}</td>
                                <td style="padding: 0.8rem 0.5rem;"><span style="padding: 0.25rem 0.6rem; border-radius: 999px; background: #dbeafe; color: #1d4ed8; font-size: 0.8rem; font-weight: 700;">{{ ucfirst($admin->role) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 1rem 0.5rem; text-align: center; color: #64748b;">No admin accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection