<a {{ $attributes->class(['relative rounded-lg px-4 py-2 hover:animate-wiggle focus:outline-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 active:text-white/70 overflow-hidden']) }}>
    <div
        class="absolute inset-0"
        style="background-image: linear-gradient(115deg, #987754 0%, #b69862 13.2%, #d9bd72 30.8%, #eed47b 44.9%, #f6dd7f 53.8%, #ecd278 57.9%, #a98449 86.7%, #8e6636 100%);"
    ></div>
    <div class="absolute inset-0.5 bg-universe rounded-md"></div>
    <span
        class="relative text-transparent bg-clip-text text-base font-semibold"
        style="background-image: linear-gradient(45deg, #987754 0%, #b69862 13.2%, #d9bd72 30.8%, #eed47b 44.9%, #f6dd7f 53.8%, #ecd278 57.9%, #a98449 86.7%, #8e6636 100%);"
    >
        {{ $slot }}
    </span>
</a>
