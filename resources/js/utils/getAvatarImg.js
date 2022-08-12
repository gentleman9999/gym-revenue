export const getAvatarImg = (firstName, lastName) => {
    return (
        "https://ui-avatars.com/api/?name=" +
        firstName[0].toUpperCase() +
        "+" +
        lastName[0].toUpperCase() +
        "&color=FFFFFF&background=0079FF"
    );
};
