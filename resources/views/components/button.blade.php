<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-[#ea580c] hover:bg-[#d94e08] text-white font-semibold py-2.5 px-5 rounded-xl shadow-sm hover:shadow-orange-500/25 transition-all cursor-pointer whitespace-nowrap']) }}>
    {{ $slot }}
</button>
