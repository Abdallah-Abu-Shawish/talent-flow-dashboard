@props(['name' => 'grid'])
<svg {{ $attributes->class(['icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('brand') <rect x="4" y="6" width="16" height="14" rx="4"/><path d="M12 3v3M8 12h.01M16 12h.01M9 16h6M1 11v4M23 11v4"/> @break
        @case('building') <path d="M4 21V3h12v18M16 9h4v12M2 21h20M8 7h4M8 11h4M8 15h4M9 21v-3h3v3"/> @break
        @case('users') <circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M21 21v-3a6 6 0 0 0-4-5.65"/> @break
        @case('briefcase') <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V3h8v4M3 12a24 24 0 0 0 18 0M10 12v3h4v-3"/> @break
        @case('interview') <rect x="3" y="4" width="18" height="14" rx="3"/><path d="m8 18-3 4v-5M8 9h8M8 13h5"/> @break
        @case('coins') <ellipse cx="9" cy="5" rx="6" ry="3"/><path d="M3 5v5c0 1.66 2.69 3 6 3M3 10v5c0 1.66 2.69 3 6 3M15 5v3"/><ellipse cx="15" cy="12" rx="6" ry="3"/><path d="M9 12v6c0 1.66 2.69 3 6 3s6-1.34 6-3v-6"/> @break
        @case('activity') <path d="M2 12h5l3-8 4 16 3-8h5"/> @break
        @case('audit') <path d="M6 3h12a2 2 0 0 1 2 2v16H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2ZM8 7h8M8 11h8M8 15h5"/> @break
        @case('shield') <path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/> @break
        @case('settings') <circle cx="12" cy="12" r="3"/><path d="m9 3-1 3-3 1-2 3 2 2-1 3 2 3 3-1 3 3 3-2 3 1 2-3-1-3 2-2-2-3-3-1-1-3Z"/> @break
        @case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
        @case('chevron') <path d="m9 5 7 7-7 7"/> @break
        @case('arrow') <path d="M5 12h14m-5-5 5 5-5 5"/> @break
        @case('back') <path d="M19 12H5m5-5-5 5 5 5"/> @break
        @case('refresh') <path d="M20 7v5h-5M4 17v-5h5M5 7a8 8 0 0 1 13-2l2 3M4 16l2 3a8 8 0 0 0 13-2"/> @break
        @case('moon') <path d="M21 13a9 9 0 0 1-10-10A9 9 0 1 0 21 13Z"/> @break
        @case('logout') <path d="M9 3H4v18h5M10 12h11m-4-4 4 4-4 4"/> @break
        @case('plus') <path d="M12 5v14M5 12h14"/> @break
        @case('check') <path d="m5 12 4 4L19 6"/> @break
        @case('info') <circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/> @break
        @case('lock') <rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4M12 14v3"/> @break
        @case('search') <circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/> @break
        @case('close') <path d="m6 6 12 12M18 6 6 18"/> @break
        @default <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
    @endswitch
</svg>
