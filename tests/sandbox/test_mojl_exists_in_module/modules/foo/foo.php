<?php
// In a real module, it would be:
// if (!$mojl) exit; // or something

if (!$mojl) throw new \Exception;
echo 'success';