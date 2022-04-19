<template>
    <a
        href="#"
        class="flex flex-row gap-2 items-center px-4 py-3 hover:bg-base-100 -mx-2 text-sm"
        :class="{'bg-opacity-[20%]': !notification.dismissed_at, 'bg-primary': !notification.dismissed_at}"
        @click.prevent="handleClick"
    >
        <template v-if="$slots.icon">
            <figure class="w-1/6 px-4">
                <slot name="icon"/>
            </figure>
        </template>
        <slot>
            {{ notification.text }}
        </slot>
    </a>
</template>

<script>
import {defineComponent} from "vue";

export default defineComponent({
    props: {
        notification: {
            required: true,
            type: Object
        }
    },
    setup(props, {emit}) {
        const handleClick = (e) => {
            emit('click', e);
            emit('dismiss', props.notification.id);
        }
        return {handleClick}
    }
});
</script>
