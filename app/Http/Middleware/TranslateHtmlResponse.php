<?php

namespace App\Http\Middleware;

use Closure;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslateHtmlResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->getLocale() !== 'ar') {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && !str_contains(strtolower($contentType), 'text/html')) {
            return $response;
        }

        if (!method_exists($response, 'getContent') || !method_exists($response, 'setContent')) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || trim($content) === '') {
            return $response;
        }

        $translated = $this->translateHtml($content);
        if (is_string($translated) && $translated !== '') {
            $response->setContent($translated);
        }

        return $response;
    }

    protected function translateHtml(string $html): string
    {
        if (!class_exists(DOMDocument::class)) {
            return $this->translateFallback($html);
        }

        $internalErrors = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $loaded = $dom->loadHTML(
            '<?xml encoding="utf-8" ?>' . $html,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        if (!$loaded) {
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);

            return $this->translateFallback($html);
        }

        $this->translateNodeTree($dom);

        $output = $dom->saveHTML() ?: $html;
        $output = preg_replace('/^<\?xml[^>]+>\s*/u', '', $output) ?: $output;

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $output;
    }

    protected function translateNodeTree(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            if ($this->shouldSkipElement($node)) {
                return;
            }

            foreach (['placeholder', 'title', 'aria-label', 'aria-placeholder'] as $attribute) {
                if ($node->hasAttribute($attribute)) {
                    $node->setAttribute($attribute, $this->translateText($node->getAttribute($attribute)));
                }
            }

            if (
                $node->tagName === 'input'
                && $node->hasAttribute('value')
                && in_array(strtolower((string) $node->getAttribute('type')), ['submit', 'button', 'reset'], true)
            ) {
                $node->setAttribute('value', $this->translateText($node->getAttribute('value')));
            }
        }

        if ($node instanceof DOMText) {
            $node->nodeValue = $this->translateText($node->nodeValue);
            return;
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $this->translateNodeTree($child);
        }
    }

    protected function shouldSkipElement(DOMElement $element): bool
    {
        $tag = strtolower($element->tagName);

        if (in_array($tag, ['script', 'style', 'noscript', 'template', 'code', 'pre'], true)) {
            return true;
        }

        if ($element->hasAttribute('data-translate') && strtolower((string) $element->getAttribute('data-translate')) === 'off') {
            return true;
        }

        return false;
    }

    protected function translateText(?string $text): string
    {
        if (!is_string($text) || trim($text) === '') {
            return (string) $text;
        }

        $translated = $this->translationMap()[$text] ?? null;
        if (is_string($translated) && $translated !== '') {
            return $translated;
        }

        $trimmed = trim($text);
        $translatedTrimmed = $this->translationMap()[$trimmed] ?? null;

        if (is_string($translatedTrimmed) && $translatedTrimmed !== '') {
            $leading = '';
            $trailing = '';

            if (preg_match('/^\s+/u', $text, $m)) {
                $leading = $m[0];
            }

            if (preg_match('/\s+$/u', $text, $m)) {
                $trailing = $m[0];
            }

            return $leading . $translatedTrimmed . $trailing;
        }

        return $text;
    }

    protected function translateFallback(string $content): string
    {
        $tokens = preg_split('/(<[^>]+>)/u', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($tokens) || $tokens === []) {
            return $content;
        }

        $result = '';
        $skipUntilTag = null;

        foreach ($tokens as $token) {
            if ($token === '') {
                continue;
            }

            if (str_starts_with($token, '<')) {
                if ($skipUntilTag !== null) {
                    $result .= $token;

                    if (preg_match('/^<\s*\/\s*' . preg_quote($skipUntilTag, '/') . '\b/i', $token)) {
                        $skipUntilTag = null;
                    }

                    continue;
                }

                if (preg_match('/^<\s*(script|style|noscript|template|code|pre)\b/i', $token, $matches)) {
                    $skipUntilTag = strtolower($matches[1]);
                    $result .= $token;
                    continue;
                }

                $result .= $this->translateTagAttributes($token);
                continue;
            }

            if ($skipUntilTag !== null) {
                $result .= $token;
                continue;
            }

            $result .= $this->translateText($token);
        }

        return $result;
    }

    protected function translateTagAttributes(string $tag): string
    {
        foreach (['placeholder', 'title', 'aria-label', 'aria-placeholder'] as $attribute) {
            $tag = preg_replace_callback(
                '/\b' . preg_quote($attribute, '/') . '=(["\'])(.*?)\1/iu',
                fn ($matches) => $attribute . '=' . $matches[1] . $this->translateText($matches[2]) . $matches[1],
                $tag
            ) ?? $tag;
        }

        if (preg_match('/^<\s*input\b/i', $tag) && preg_match('/\btype=(["\'])(submit|button|reset)\1/iu', $tag)) {
            $tag = preg_replace_callback(
                '/\bvalue=(["\'])(.*?)\1/iu',
                fn ($matches) => 'value=' . $matches[1] . $this->translateText($matches[2]) . $matches[1],
                $tag
            ) ?? $tag;
        }

        return $tag;
    }

    protected function translationMap(): array
    {
        static $map = null;

        if ($map !== null) {
            return $map;
        }

        $map = [];

        $arPath = resource_path('lang/ar.json');
        $ar = file_exists($arPath) ? json_decode((string) file_get_contents($arPath), true) : [];

        if (is_array($ar)) {
            foreach ($ar as $key => $value) {
                if (!is_string($key) || !is_string($value)) {
                    continue;
                }

                if (trim($key) === '' || $key === $value) {
                    continue;
                }

                $map[$key] = $value;
            }
        }

        $extra = [
            'Welcome,' => 'مرحباً،',
            'Welcome' => 'مرحباً',
            'This Month' => 'هذا الشهر',
            'This Year' => 'هذا العام',
            'This Week' => 'هذا الأسبوع',
            'Today' => 'اليوم',
            'Recently' => 'مؤخراً',
            'Open' => 'مفتوح',
            'Closed' => 'مغلق',
            'Resolved' => 'تم الحل',
            'In Progress' => 'قيد المعالجة',
            'Pending' => 'قيد الانتظار',
            'Approved' => 'مقبول',
            'Rejected' => 'مرفوض',
            'Under Review' => 'قيد المراجعة',
            'Active' => 'نشط',
            'Inactive' => 'غير نشط',
            'Featured' => 'مميز',
            'Published' => 'منشور',
            'Draft' => 'مسودة',
            'Upcoming' => 'قادم',
            'Completed' => 'مكتمل',
            'Cancelled' => 'ملغي',
            'Online' => 'عن بُعد',
            'Name' => 'الاسم',
            'Email' => 'البريد الإلكتروني',
            'Role' => 'الدور',
            'Status' => 'الحالة',
            'Date' => 'التاريخ',
            'Actions' => 'الإجراءات',
            'View' => 'عرض',
            'Details' => 'التفاصيل',
            'View Details' => 'عرض التفاصيل',
            'Manage' => 'إدارة',
            'Create' => 'إنشاء',
            'Edit' => 'تعديل',
            'Update' => 'تحديث',
            'Save' => 'حفظ',
            'Submit' => 'إرسال',
            'Search' => 'بحث',
            'Reset' => 'إعادة تعيين',
            'Filter' => 'تصفية',
            'Show' => 'عرض',
            'Hide' => 'إخفاء',
            'Close' => 'إغلاق',
            'Back' => 'رجوع',
            'Delete' => 'حذف',
            'Reject' => 'رفض',
            'Approve' => 'اعتماد',
            'Suspend' => 'تعليق',
            'Activate' => 'تفعيل',
            'Applicants' => 'المتقدمون',
            'Created:' => 'تاريخ الإنشاء:',
            'Updated:' => 'آخر تحديث:',
            'Date:' => 'التاريخ:',
            'Deadline:' => 'آخر موعد:',
        ];

        $map = $extra + $map;

        uksort($map, static function (string $a, string $b): int {
            return mb_strlen($b) <=> mb_strlen($a);
        });

        return $map;
    }
}
