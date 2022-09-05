<template>
    <teleport to="body">
        <div class="modal" :class="{ 'modal-open': isOpen }">
            <div v-if="closable" class="absolute inset-0" @click="close"></div>
            <div
                class="modal-box bg-base-200 max-h-[90vh] max-w-[90vw] overflow-auto border-[1px] border-secondary w-fit pt-8 rounded-lg"
                v-bind="$attrs"
            >
                <button
                    type="button"
                    class="btn btn-ghost absolute top-0 right-0"
                    @click="close"
                    v-if="closable && showCloseButton"
                >
                    x
                </button>

                <slot />
                <template v-if="$slots?.actions">
                    <div class="modal-action">
                        <slot name="actions"></slot>
                    </div>
                </template>
            </div>
        </div>
    </teleport>
</template>
<style scoped>
.modal {
    @apply items-center;
    max-width: 100vw;
}
</style>
<script>
import {
    defineComponent,
    ref,
    watchEffect,
    onMounted,
    onBeforeUnmount,
} from "vue";
import { useLockScroll } from "vue-composable";

export default defineComponent({
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        closable: {
            type: Boolean,
            default: true,
        },
        showCloseButton: {
            type: Boolean,
            default: true,
        },
    },
    setup(props, { emit }) {
        const { locked, lock, unlock } = useLockScroll("body", "no-scroll");
        onMounted(() => {
            if (!props.open) {
                unlock();
            }
        }); //todo: use another package or roll pour own lock scroll. we shouldn't have to call unlock on mount

        const isOpen = ref(!!props.open);

        // const close = () => (isOpen.value = false);
        // const open = () => (isOpen.value = true);

        const close = () => {
            isOpen.value = false;
            if (locked) {
                unlock();
            }
            emit("close");
        };
        const open = () => {
            isOpen.value = true;
            emit("open");
            lock();
        };

        watchEffect(() => {
            if (props.open) {
                open();
            } else {
                close();
                emit("close");
            }
        });

        watchEffect(() => {
            if (isOpen.value === true) {
                lock();
            } else {
                unlock();
            }
        });

        onBeforeUnmount(() => {
            unlock();
        });

        return { isOpen, close, open };
    },
    emits: ["close", "open"],
});
</script>
