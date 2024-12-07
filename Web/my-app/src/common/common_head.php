<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&display=swap" rel="stylesheet">

<script>
tailwind.config = {
    theme: {
    extend: {
        fontFamily: {
        'shodou': ['Shodou', 'sans-serif'],
        },
        animation: {
        'gradient-text': 'gradient-text 5s ease infinite',
        },
        keyframes: {
        'gradient-text': {
            '0%, 100%': { backgroundPosition: '0% 50%' },
            '50%': { backgroundPosition: '100% 50%' },
        }
        },
    }
    }
}
</script>

<style>
@font-face {
    font-family: "Shodou";
    src: url("../tmp_m.ttf") format('truetype');
}

.gradient-text {
    background: linear-gradient(90deg, red, orange, yellow, green, blue, indigo, violet);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    color: transparent;
    animation: gradient-text 5s ease infinite;
}
</style>