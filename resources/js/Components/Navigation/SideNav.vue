<template>
    <div class="rounded-lg shadow bg-base-200 drawer drawer-mobile h-full fixed z-8">
        <input id="my-drawer-2" type="checkbox" class="drawer-toggle"/>
        <div class="flex flex-col items-center justify-center drawer-content fixed lg:hidden">
            <label
                for="my-drawer-2"
                class="mb-4 btn btn-primary drawer-button lg:hidden"
            >
                open menu
            </label>
            <!-- <div class="hidden text-xs text-center lg:block">
              Menu is always open on desktop size. <br />Resize the browser to see
              toggle button on mobile size
            </div>
            <div class="text-xs text-center lg:hidden">
              Menu can be toggled on mobile size. <br />Resize the browser to see
              fixed sidebar on desktop size
            </div> -->
        </div>
        <div class="drawer-side fixed h-screen z-8">
            <label for="my-drawer-2" class="drawer-overlay"></label>
            <ul class="menu p-4 overflow-y-auto w-80 bg-base-100 text-base-content mt-16">
                <li>
                    <a>Menu Item</a>
                </li>
                <li>
                    <a>Menu Item</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import {defineComponent} from "vue";

export default defineComponent({
    setup() {
        return {};
    },
});
</script>
