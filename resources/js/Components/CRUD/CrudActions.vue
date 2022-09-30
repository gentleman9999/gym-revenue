<template>
    <div class="dropdown dropdown-end">
        <slot name="trigger">
            <div class="btn btn-ghost btn-sm" tabindex="0" @click.prevent.stop>
                <font-awesome-icon icon="ellipsis-h" size="lg" />
            </div>
        </slot>
        <slot name="before" />
        <slot>
            <ul
                tabindex="0"
                class="p-2 shadow menu dropdown-content text-base-content bg-base-300 rounded-box w-52"
            >
                <li v-for="[key, action] in Object.entries(actions)" :key="key">
                    <a
                        @click.prevent.stop="
                            () => action.handler({ data, baseRoute })
                        "
                        href="#"
                    >
                        {{ action.label }}
                    </a>
                </li>
            </ul>
        </slot>
        <slot name="after" />
    </div>
</template>
<script>
import { defineComponent, computed, ref } from "vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { getActions, getRenderableActions } from "./helpers/actions";

library.add(faEllipsisH);

export default defineComponent({
    props: {
        actions: {
            type: Object,
            default: {},
        },
        data: {
            type: Object,
            required: true,
        },
        baseRoute: {
            type: String,
            required: true,
        },
        hasPreviewComponent: {
            type: Boolean,
            default: false,
        },
        modelName: {
            type: String,
            required: true,
        },
    },
    components: {
        FontAwesomeIcon,
    },
    setup(props) {
        let actions = props.actions;
        if (!props instanceof Array) {
            actions = getActions(props);
        }
        actions = getRenderableActions(props);
        return { actions };
    },
});
</script>
