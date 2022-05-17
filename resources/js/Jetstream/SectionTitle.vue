<template>
    <div class="md:col-span-2 flex justify-between">
        <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium ">
                <slot name="title"></slot>
            </h3>

            <p class="mt-1 text-sm ">
                <slot name="description"></slot>
            </p>
        </div>

        <div class="px-4 sm:px-0">
            <slot name="aside"></slot>
        </div>
    </div>
</template>
