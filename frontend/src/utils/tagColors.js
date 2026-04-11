export function getTagStyle(id) {
    const hue = (id * 20) % 360

    const isLightHue = hue >= 40 && hue <= 200

    const bgLightness = isLightHue ? 75 : 85
    const textLightness = isLightHue ? 25 : 45

    return {
        backgroundColor: `hsl(${hue}, 85%, ${bgLightness}%)`,
        color: `hsl(${hue}, 85%, ${textLightness}%)`,
        border: `2px solid hsl(${hue}, 85%, ${textLightness}%)`
    }
}