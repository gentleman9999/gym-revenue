<template>
    <div>
        <button :class="classes" class="w-full text-left" v-if="as == 'button'">
            <slot></slot>
        </button>

        <Link :href="href" :class="classes" v-else>
            <slot />
        </Link>
    </div>
</template>

<script>
    import { defineComponent } from 'vue';
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Link,
        },
        props: ['active', 'href', 'as'],

        computed: {
            classes() {
                return this.active
                    ? 'block pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-medium  bg-indigo-50 focus:outline-none focus:bg-indigo-100 focus:border-indigo-700 transition'
                    : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium  hover:bg-base-100 hover:border-base-100-300 focus:outline-none focus:bg-base-100 focus:border-base-100-300 transition'
            }
        }
    })
</script>
