<template>
    <teleport to="body">
        <div class="modal" :class="{ 'modal-open': isOpen }">
            <div v-if="closable" class="absolute inset-0" @click="close"></div>
            <div class="modal-box" v-bind="$attrs">
                <button
                    type="button"
                    class="btn btn-ghost absolute top-2 right-2"
                    @click="close"
                    v-if="closable"
                >
                    x
                </button>

                <slot />
                <template v-if="$slots.footer">
                    <div class="modal-action">
                        <slot name="actions"></slot>
                    </div>
                </template>
            </div>
        </div>
    </teleport>
</template>

<script>
import { defineComponent, ref, watchEffect, onMounted } from "vue";
import { useLockScroll } from "vue-composable";

export default defineComponent({
    props: {
        id: {
            type: String,
            required: true,
        },
        open: {
            type: Boolean,
            default: false,
        },
        closable: {
            type: Boolean,
            default: true,
        },
    },
    setup(props, {emit}) {
        const { locked, lock, unlock } = useLockScroll("body", "no-scroll");
        onMounted(unlock); //todo: use another package or roll pour own lock scroll. we shouldn't have to call unlock on mount

        const isOpen = ref(!!props.open);

        // const close = () => (isOpen.value = false);
        // const open = () => (isOpen.value = true);

        const close = () => {
            isOpen.value = false;
            if (locked) {
                unlock();
            }
            emit('close');
        };
        const open = () => {
            isOpen.value = true;
            emit('open');
            // lock();
        };

        watchEffect(() => {
            if (props.open) {
                open();
            } else {
                close();
            }
        });

        watchEffect(() => {
            if (isOpen.value === true) {
                lock();
            } else {
                unlock();
            }
        });

        return { isOpen, close, open };
    },
});
</script>
