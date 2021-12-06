<template>
    <tr class="hover">
        <td v-for="(field, index) in fieldKeys" class="col-span-3 truncate">
            <component v-if="fields[index].component" :is="fields[index].component" v-bind="{[modelName]: data, data }">
                {{ data[field] }}
            </component>
            <template v-else>
                <vue-json-pretty
                    v-if="isObject(data[field])"
                    :data="data[field]"
                />
                <span
                    v-else
                    :title="
                        isObject(field)
                            ? field.transform(data[field.name])
                            : data[field]
                    "
                >
                    {{
                        isObject(field)
                            ? field.transform(data[field.name])
                            : data[field]
                    }}
                </span>
            </template>
        </td>
        <td  v-if="actions.length">
            <slot name="actions">
                <div class="dropdown dropdown-end">
                    <button class="btn btn-ghost">
                        <font-awesome-icon
                            icon="ellipsis-h"
                            size="lg"
                        />
                    </button>
                    <ul
                        tabindex="0"
                        class="p-2 shadow menu dropdown-content bg-base-300 rounded-box w-52"
                    >
                        <li v-for="action in Object.values(actions)">
                            <a @click.prevent="action.handler" href="#">{{ action.label }}</a>
                        </li>
                    </ul>
                </div>
            </slot>
        </td>
    </tr>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject, merge } from "lodash";
import { transforms, defaults as defaultTransforms } from "./transforms";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import {Inertia} from "@inertiajs/inertia";
import {computed} from "vue";

library.add(faEllipsisH);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        actions: {
            type: Object,
            default: {}
        },
        baseUrl:{
            type: String,
        }
    },
    setup(props) {
        const fieldKeys = computed(()=> {
            let __fields = [];
            if (props.fields) {
                console.log({fields: props.fields})
                __fields = props.fields.map((header) =>
                    isObject(header) ? header.label : header
                )
                __fields = __fields.map((field) =>
                    field in defaultTransforms
                        ? { name: field, transform: defaultTransforms[field] }
                        : field
                );
            } else{
                __fields =  Object.keys(props.data);
            }



            return __fields;
        });


        let __baseUrl = props.baseUrl || props.modelNamePlural || props.modelName+ 's';
        let defaultActions = {
            edit: { label: "Edit", handler: () => {Inertia.visit(route(`${__baseUrl}.edit`, props.data.id))} },
            trash: { label: "Trash", handler: () => {Inertia.visit(route('files.trash', props.data.id))} },
        };
        let __actions = Object.values(merge(defaultActions, props.actions)).filter(action=>action);

        return { fieldKeys, isObject, actions: __actions, baseUrl: __baseUrl };
    },
};
</script>
