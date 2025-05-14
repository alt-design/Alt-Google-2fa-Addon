module.exports = {
    content: [
        './resources/views/*.blade.php',
        './resources/js/Components/*.vue',
    ],
    plugins: [
        require('@tailwindcss/typography'),
    ],
    theme: {
        extend: {
            colors: {
                cpGreen: 'rgb(22 163 74)',
            }
        }
    },
    safelist: [
        'bg-slate-400',
        'text-slate-400',
        'bg-amber-300',
        'text-amber-300',
        'bg-cpGreen',
        'text-cpGreen',
    ]
}
