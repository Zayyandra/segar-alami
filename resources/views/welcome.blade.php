<?php
if (auth()->check()) {
    redirect()->route('app.dashboard')->send();
} else {
    redirect()->route('login')->send();
}
