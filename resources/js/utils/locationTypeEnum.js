export const LOCATION_TYPES = Object.freeze({
    /** @see App\Enums\LocationTypeEnum::STORE */
    STORE: "store",
});

export const parseLocationTypeDisplayName = (location_type) => {
    switch (location_type.value) {
        case LOCATION_TYPES.STORE:
            return "gym";
        default:
            return location_type.value;
    }
};
