import { inject, provide, shallowRef } from "vue";
import { modalSlotRef } from "./symbols";

export const provider = () => {
    const teleportRef = shallowRef(null);

    const setTelRef = (el) => {
        teleportRef.value = el;
    };

    provide(modalSlotRef, setTelRef);

    return teleportRef;
};

export const injector = () => inject(modalSlotRef);
