/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin');

module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Kosugi', 'Arial', 'sans-serif'],
      },
      colors: {
        themeYellow: "#FFC222",
        themeBeige: "#FFECC3",
        themeBrown: "#B47B40",
        themeInput: "#EBE4DA",
        themeGray: "#EBE4DA",
        limeGreen: "#D7F8DA",
        limeGreenChecked: '#95F4A0',
        themeOrange:"#F78F20",
      },
      zIndex: {
        '1': '1',
        '2': '2',
        '10': '10',
      },
      width: {
        '18' : '4.5rem',
        '100' : '25rem',
        '104' : '26rem',
        '108' : '27rem',
        '112' : '28rem',
        '116' : '29rem',
        '120' : '30rem',
        '124' : '31rem',
        '128' : '32rem',
        '132' : '33rem',
        '136' : '34rem',
        '140' : '35rem',
        '144' : '36rem',
      },
      height : {
        '18' : '4.5rem',
        '100' : '25rem',
        '104' : '26rem',
        '108' : '27rem',
        '112' : '28rem',
        '116' : '29rem',
        '120' : '30rem',
        '124' : '31rem',
        '128' : '32rem',
        '132' : '33rem',
        '136' : '34rem',
        '140' : '35rem',
        '144' : '36rem',
      }
    },
  },
  plugins: [
    plugin(function({ addVariant }) {
      addVariant('contenteditable', '&[contenteditable="true"]');
    })
  ],
  safelist: [
    "sb-redirect",
    "signup-fieldset",
    "profile-img",
  ],
};
