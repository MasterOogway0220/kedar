// =================== preloader js  ================== //
document.addEventListener('DOMContentLoaded', function () {
    var preloader = document.querySelector('.preloader');
    preloader.style.transition = 'opacity 0.5s ease';
    // Hide the preloader 1 second (1000 milliseconds) after DOM content is loaded
    setTimeout(function () {
        preloader.style.opacity = '0';
        setTimeout(function () {
            preloader.style.display = 'none';
        }, 500); // .5 seconds for the fade-out transition
    }, 1000); // 1 second delay before starting the fade-out effect
});
// =================== preloader js end ================== //


// =================== light and dark start ================== //

const colorSwitcher = document.getElementById('btnSwitch');


switchThemeByUrl();
updateThemeColor(localStorage.getItem('theme'))


colorSwitcher.addEventListener('click', () => {

    const theme = localStorage.getItem('theme');

    if (theme && theme === 'dark') {

        updateThemeColor('light');

    } else {
        updateThemeColor('dark');

    }

});

function updateThemeColor(themeMode = 'light') {

    document.documentElement.setAttribute('data-bs-theme', themeMode);
    localStorage.setItem('theme', themeMode)

    if (themeMode === 'dark') {
        colorSwitcher.classList.add('dark-switcher');

    } else {
        colorSwitcher.classList.remove('dark-switcher');
    }

    changeImage(themeMode);

}



function switchThemeByUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const theme = urlParams.get('theme');

    if (theme) {
        localStorage.setItem("theme", theme);
    }

}

// =================== light and dark end ================== //




// =================== Change image path start ================== //


function changeImage(themeMode = 'light') {

    const icon = document.querySelector('#btnSwitch img');


    if (themeMode === "dark") {

        icon.src = './assets/images/icon/sun.svg';
        var images = document.querySelectorAll('img.dark');

        for (var i = 0; i < images.length; i++) {
            var oldSrc = images[i].src;
            oldSrc = oldSrc.replace("-dark.", ".");
            var oldIndex = oldSrc.lastIndexOf(".");
            var baseName = oldSrc.slice(0, oldIndex);
            var extension = oldSrc.slice(oldIndex);
            var newSrc = baseName + "-dark" + extension;
            images[i].src = newSrc;
        }
    } else {
        icon.src = './assets/images/icon/moon.svg';
        var images = document.querySelectorAll('img.dark');

        for (var i = 0; i < images.length; i++) {
            var oldSrc = images[i].src;
            var newSrc = oldSrc.replace("-dark.", ".");
            images[i].src = newSrc;
        }
    }

}


// =================== Change image path end ================== //









// =================== header js start here ===================


// Add class 'menu-item-has-children' to parent li elements of '.submenu'
var submenuList = document.querySelectorAll("ul>li>.submenu");
submenuList.forEach(function (submenu) {
    var parentLi = submenu.parentElement;
    if (parentLi) {
        parentLi.classList.add("menu-item-has-children");
    }
});

// Fix dropdown menu overflow problem
var menuList = document.querySelectorAll("ul");
menuList.forEach(function (menu) {
    var parentLi = menu.parentElement;
    if (parentLi) {
        parentLi.addEventListener("mouseover", function () {
            var menuPos = menu.getBoundingClientRect();
            if (menuPos.left + menu.offsetWidth > window.innerWidth) {
                menu.style.left = -menu.offsetWidth + "px";
            }
        });
    }
});



// Toggle menu on click

var menuLinks = document.querySelectorAll(".menu li a");

menuLinks.forEach(function (link) {
    link.addEventListener("click", function (e) {
        e.stopPropagation(); // prevent the event from bubbling up to parent elements
        var element = link.parentElement;
        if (parseInt(window.innerWidth, 10) < 1200) {
            if (element.classList.contains("open")) {
                element.classList.remove("open");
                element.querySelector("ul").style.display = "none";
            } else {
                element.classList.add("open");
                element.querySelector("ul").style.display = "block";
            }
        }
    });
});




// Toggle header bar on click
var headerBar = document.querySelector(".header-bar");
function toggleMenu() {
    headerBar.classList.toggle("active");
    var menu = document.querySelector(".menu");
    if (menu) {
        menu.classList.toggle("active");
    }
    var isExpanded = headerBar.classList.contains("active");
    headerBar.setAttribute("aria-expanded", isExpanded.toString());
}
headerBar.addEventListener("click", toggleMenu);
headerBar.addEventListener("keydown", function (e) {
    if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        toggleMenu();
    }
});




//Header
var fixedTop = document.querySelector("header");
window.addEventListener("scroll", function () {
    if (window.scrollY > 300) {
        fixedTop.classList.add("header-fixed", "fadeInUp");
    } else {
        fixedTop.classList.remove("header-fixed", "fadeInUp");
    }
});


// =================== header js end here =================== //




//Animation on Scroll initializing
AOS.init();




// =================== custom trk slider js here =================== //

// component slider here
const Partner = new Swiper('.partner__slider', {
    spaceBetween: 24,
    grabCursor: true,
    loop: true,
    slidesPerView: 2,
    breakpoints: {
        576: {
            slidesPerView: 3,
        },
        768: {
            slidesPerView: 4,
        },
        992: {
            slidesPerView: 5,
            spaceBetween: 15,
        },
        1200: {
            slidesPerView: 6,
            spaceBetween: 25,
        },
    },
    autoplay: {
        delay: 1,
        disableOnInteraction: true,
    },
    speed: 2000,
});



// blog  slider here
const blog = new Swiper('.blog__slider', {
    spaceBetween: 24,
    grabCursor: true,
    loop: true,
    slidesPerView: 1,
    breakpoints: {
        576: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
        1200: {
            slidesPerView: 3,
        }
    },

    autoplay: true,
    speed: 500,
    navigation: {
        nextEl: ".blog__slider-next",
        prevEl: ".blog__slider-prev",
    },
});

// testimonial slider

const testimonial = new Swiper('.testimonial__slider', {
    spaceBetween: 24,
    grabCursor: true,
    loop: true,
    slidesPerView: 1,
    breakpoints: {
        576: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 2,
        },
        1200: {
            slidesPerView: 2,
            spaceBetween: 25,
        },
    },

    autoplay: true,
    speed: 500,

    navigation: {
        nextEl: ".testimonial__slider-next",
        prevEl: ".testimonial__slider-prev",
    },
});


// testimonial slider 2
const testimonial2 = new Swiper('.testimonial__slider2', {
    spaceBetween: 24,
    grabCursor: true,
    loop: true,
    slidesPerView: 1,
    breakpoints: {
        576: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
        1200: {
            slidesPerView: 3,
            spaceBetween: 25,
        },
    },

    autoplay: true,
    speed: 500,

    navigation: {
        nextEl: ".testimonial__slider-next",
        prevEl: ".testimonial__slider-prev",
    },
});



// testimonial slider

const testimonial3 = new Swiper('.testimonial__slider3', {
    spaceBetween: 24,
    grabCursor: true,
    loop: true,
    slidesPerView: 1,
    autoplay: true,
    speed: 500,
});

// roadmap slider 
const roadmapSlider = new Swiper('.roadmap__slider', {

    grabCursor: true,
    // loop: true,
    slidesPerView: 1,
    breakpoints: {
        576: {
            slidesPerView: 1,
            spaceBetween: 15,
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 15,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 10,
        },
        1200: {
            slidesPerView: 4,
            spaceBetween: 10,
        },
        1400: {
            slidesPerView: 4,
            spaceBetween: 10,
        }

    },

    autoplay: true,
    speed: 500,

});

// =================== custom trk slider end here =================== //




// =================== scroll js start here =================== //

// Show/hide button on scroll
window.addEventListener('scroll', function () {
    var scrollToTop = document.querySelector('.scrollToTop');

    if (scrollToTop) {
        if (window.pageYOffset > 300) {
            scrollToTop.style.bottom = '7%';
            scrollToTop.style.opacity = '1';
            scrollToTop.style.transition = 'all .5s ease';
        } else {
            scrollToTop.style.bottom = '-30%';
            scrollToTop.style.opacity = '0';
            scrollToTop.style.transition = 'all .5s ease';
        }
    }
});

var scrollToTop = document.querySelector('.scrollToTop');

if (scrollToTop) {

    // Click event to scroll to top
    scrollToTop.addEventListener('click', function (e) {
        e.preventDefault();
        var scrollDuration = 100; // Set scroll duration in milliseconds
        var scrollStep = -window.scrollY / (scrollDuration / 15);
        var scrollInterval = setInterval(function () {
            if (window.scrollY !== 0) {
                window.scrollBy(0, scrollStep);
            } else {
                clearInterval(scrollInterval);
            }
        }, 15);
    });
}

// =================== scroll js end here =================== //



// =================== count start here =================== //
new PureCounter();
// =================== count end here =================== //




// =================== rtl icon direction chnage start here =================== //
// Get the HTML tag
const htmlTag = document.querySelector('html');

// Function to toggle the icon directions
function toggleAllIconsDirection() {
    const icons = document.querySelectorAll('i');

    icons.forEach((icon) => {
        if (icon.classList.contains("fa-arrow-right") || icon.classList.contains("fa-angle-right")) {
            icon.classList.remove("fa-arrow-right", "fa-angle-right");
            icon.classList.add("fa-arrow-left", "fa-angle-left");
        } else if (icon.classList.contains("fa-arrow-left") || icon.classList.contains("fa-angle-left")) {
            icon.classList.remove("fa-arrow-left", "fa-angle-left");
            icon.classList.add("fa-arrow-right", "fa-angle-right");
        }
    });
}

// Check if the HTML tag has the dir="rtl" attribute
if (htmlTag.getAttribute('dir') === 'rtl') {
    toggleAllIconsDirection();
}
// =================== rtl icon direction chnage end here =================== //

// Accessibility: Compliance dropdown keyboard support
document.querySelectorAll('.menu li > a[aria-haspopup]').forEach(function(toggle) {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        var expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', (!expanded).toString());
    });
    toggle.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', (!expanded).toString());
            var submenu = this.parentElement.querySelector('.submenu');
            if (submenu) {
                if (!expanded) {
                    submenu.style.display = 'block';
                    var firstLink = submenu.querySelector('a');
                    if (firstLink) firstLink.focus();
                } else {
                    submenu.style.display = '';
                }
            }
        }
        if (e.key === 'Escape') {
            this.setAttribute('aria-expanded', 'false');
            var submenu = this.parentElement.querySelector('.submenu');
            if (submenu) submenu.style.display = '';
        }
    });
});

// Accessibility: Escape key closes submenus
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Close any open submenus
        document.querySelectorAll('.menu li > a[aria-haspopup]').forEach(function(toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            var submenu = toggle.parentElement.querySelector('.submenu');
            if (submenu) submenu.style.display = '';
        });
        // Close mobile menu if open
        var headerBar = document.querySelector('.header-bar');
        var menu = document.querySelector('.menu');
        if (headerBar && headerBar.classList.contains('active')) {
            headerBar.classList.remove('active');
            headerBar.setAttribute('aria-expanded', 'false');
            if (menu) menu.classList.remove('active');
            headerBar.focus();
        }
    }
});

// =================== Accessibility Toolbar =================== //
function setFontSize(size) {
    var html = document.documentElement;
    html.classList.remove('font-size-large', 'font-size-largest');
    if (size === 'large') html.classList.add('font-size-large');
    if (size === 'largest') html.classList.add('font-size-largest');
    localStorage.setItem('a11y-font-size', size);

    // Update active button state
    document.querySelectorAll('.a11y-toolbar button').forEach(function(btn) {
        btn.classList.remove('active');
    });
    var labels = {'normal': 'Normal text size', 'large': 'Large text size', 'largest': 'Largest text size'};
    var activeBtn = document.querySelector('.a11y-toolbar button[aria-label="' + labels[size] + '"]');
    if (activeBtn) activeBtn.classList.add('active');
}

function toggleContrast() {
    var html = document.documentElement;
    html.classList.toggle('high-contrast');
    var isHigh = html.classList.contains('high-contrast');
    localStorage.setItem('a11y-high-contrast', isHigh);
    var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle high contrast mode"]');
    if (btn) {
        btn.classList.toggle('active', isHigh);
        btn.setAttribute('aria-pressed', isHigh.toString());
    }
}

// Restore accessibility preferences on page load
(function() {
    var fontSize = localStorage.getItem('a11y-font-size');
    if (fontSize && fontSize !== 'normal') {
        document.documentElement.classList.add('font-size-' + fontSize);
    }
    var highContrast = localStorage.getItem('a11y-high-contrast');
    if (highContrast === 'true') {
        document.documentElement.classList.add('high-contrast');
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (fontSize) setFontSize(fontSize);
        if (highContrast === 'true') {
            var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle high contrast mode"]');
            if (btn) {
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
            }
        }
    });
})();
// Grayscale toggle
function toggleGrayscale() {
    var html = document.documentElement;
    html.classList.toggle('grayscale');
    var isOn = html.classList.contains('grayscale');
    localStorage.setItem('a11y-grayscale', isOn);
    var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle grayscale mode"]');
    if (btn) {
        btn.classList.toggle('active', isOn);
        btn.setAttribute('aria-pressed', isOn.toString());
    }
}

// Dyslexia-friendly font toggle
function toggleDyslexiaFont() {
    var html = document.documentElement;
    html.classList.toggle('dyslexia-font');
    var isOn = html.classList.contains('dyslexia-font');
    localStorage.setItem('a11y-dyslexia-font', isOn);
    var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle dyslexia-friendly font"]');
    if (btn) {
        btn.classList.toggle('active', isOn);
        btn.setAttribute('aria-pressed', isOn.toString());
    }
}

// Line spacing toggle
function toggleLineSpacing() {
    var html = document.documentElement;
    html.classList.toggle('line-spacing-wide');
    var isOn = html.classList.contains('line-spacing-wide');
    localStorage.setItem('a11y-line-spacing', isOn);
    var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle increased line spacing"]');
    if (btn) {
        btn.classList.toggle('active', isOn);
        btn.setAttribute('aria-pressed', isOn.toString());
    }
}

// Reading guide (ruler that follows mouse)
var readingGuideEl = null;
function toggleReadingGuide() {
    if (!readingGuideEl) {
        readingGuideEl = document.createElement('div');
        readingGuideEl.className = 'reading-guide';
        readingGuideEl.setAttribute('aria-hidden', 'true');
        document.body.appendChild(readingGuideEl);
    }
    readingGuideEl.classList.toggle('active');
    var isOn = readingGuideEl.classList.contains('active');
    localStorage.setItem('a11y-reading-guide', isOn);
    var btn = document.querySelector('.a11y-toolbar button[aria-label="Toggle reading guide"]');
    if (btn) {
        btn.classList.toggle('active', isOn);
        btn.setAttribute('aria-pressed', isOn.toString());
    }
}

document.addEventListener('mousemove', function(e) {
    if (readingGuideEl && readingGuideEl.classList.contains('active')) {
        readingGuideEl.style.top = (e.clientY - 6) + 'px';
    }
});

// Text-to-Speech (Read Aloud)
var ttsUtterance = null;
var ttsActive = false;

function toggleReadAloud() {
    if (!('speechSynthesis' in window)) {
        alert('Text-to-Speech is not supported in your browser. Please try Chrome, Edge, or Safari.');
        return;
    }

    var btn = document.querySelector('.a11y-toolbar button[aria-label="Read page content aloud"]');

    if (ttsActive) {
        // Stop reading
        window.speechSynthesis.cancel();
        ttsActive = false;
        if (btn) {
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-volume-up" aria-hidden="true"></i> Read Aloud';
        }
        // Remove highlights
        document.querySelectorAll('.tts-speaking').forEach(function(el) {
            el.classList.remove('tts-speaking');
        });
        return;
    }

    // Gather text from main content
    var mainContent = document.querySelector('main') || document.querySelector('body');
    var sections = mainContent.querySelectorAll('h1, h2, h3, h4, h5, h6, p, li, label, td, th');
    var textParts = [];

    sections.forEach(function(el) {
        var text = el.textContent.trim();
        if (text && text.length > 1 && el.offsetParent !== null) {
            textParts.push({ element: el, text: text });
        }
    });

    if (textParts.length === 0) {
        alert('No readable content found on this page.');
        return;
    }

    ttsActive = true;
    if (btn) {
        btn.classList.add('active');
        btn.innerHTML = '<i class="fas fa-stop" aria-hidden="true"></i> Stop Reading';
    }

    var currentIndex = 0;

    function speakNext() {
        if (currentIndex >= textParts.length || !ttsActive) {
            ttsActive = false;
            if (btn) {
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-volume-up" aria-hidden="true"></i> Read Aloud';
            }
            document.querySelectorAll('.tts-speaking').forEach(function(el) {
                el.classList.remove('tts-speaking');
            });
            return;
        }

        var part = textParts[currentIndex];

        // Highlight current element
        document.querySelectorAll('.tts-speaking').forEach(function(el) {
            el.classList.remove('tts-speaking');
        });
        part.element.classList.add('tts-speaking');
        part.element.scrollIntoView({ behavior: 'smooth', block: 'center' });

        ttsUtterance = new SpeechSynthesisUtterance(part.text);
        ttsUtterance.lang = 'en-IN';
        ttsUtterance.rate = 0.9;
        ttsUtterance.onend = function() {
            currentIndex++;
            speakNext();
        };
        ttsUtterance.onerror = function() {
            currentIndex++;
            speakNext();
        };

        window.speechSynthesis.speak(ttsUtterance);
    }

    speakNext();
}

// Stop TTS when navigating away
window.addEventListener('beforeunload', function() {
    if (ttsActive && window.speechSynthesis) {
        window.speechSynthesis.cancel();
    }
});

// Restore all accessibility preferences on page load
(function() {
    var prefs = {
        'a11y-grayscale': 'grayscale',
        'a11y-dyslexia-font': 'dyslexia-font',
        'a11y-line-spacing': 'line-spacing-wide'
    };
    for (var key in prefs) {
        if (localStorage.getItem(key) === 'true') {
            document.documentElement.classList.add(prefs[key]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Restore button active states
        var btnMap = {
            'a11y-grayscale': 'Toggle grayscale mode',
            'a11y-dyslexia-font': 'Toggle dyslexia-friendly font',
            'a11y-line-spacing': 'Toggle increased line spacing',
            'a11y-reading-guide': 'Toggle reading guide'
        };
        for (var key in btnMap) {
            if (localStorage.getItem(key) === 'true') {
                var btn = document.querySelector('.a11y-toolbar button[aria-label="' + btnMap[key] + '"]');
                if (btn) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-pressed', 'true');
                }
            }
        }
        // Restore reading guide
        if (localStorage.getItem('a11y-reading-guide') === 'true') {
            toggleReadingGuide();
        }
    });
})();
// =================== Accessibility Toolbar end =================== //


// Accessibility: Contact form validation
var contactForm = document.querySelector('.contact__form form');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var isValid = true;
        var firstError = null;

        // Clear previous errors
        contactForm.querySelectorAll('.error-message').forEach(function(el) {
            el.textContent = '';
        });
        contactForm.querySelectorAll('.form-control').forEach(function(el) {
            el.classList.remove('is-invalid');
        });

        var name = document.getElementById('name');
        var email = document.getElementById('email');
        var textarea = document.getElementById('textarea');

        if (!name.value.trim()) {
            document.getElementById('name-error').textContent = 'Please enter your name.';
            name.classList.add('is-invalid');
            if (!firstError) firstError = name;
            isValid = false;
        }

        if (!email.value.trim()) {
            document.getElementById('email-error').textContent = 'Please enter your email address.';
            email.classList.add('is-invalid');
            if (!firstError) firstError = email;
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            document.getElementById('email-error').textContent = 'Please enter a valid email address (e.g., name@example.com).';
            email.classList.add('is-invalid');
            if (!firstError) firstError = email;
            isValid = false;
        }

        if (!textarea.value.trim()) {
            document.getElementById('textarea-error').textContent = 'Please enter your message.';
            textarea.classList.add('is-invalid');
            if (!firstError) firstError = textarea;
            isValid = false;
        }

        if (!isValid && firstError) {
            firstError.focus();
            return;
        }

        // If valid, show success
        var status = document.getElementById('form-status');
        if (status) {
            status.className = 'success';
            status.textContent = 'Thank you! Your message has been sent successfully.';
        }
    });
}