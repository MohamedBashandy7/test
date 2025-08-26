# استخدام Laravel Policies بدلاً من التحقق اليدوي

## نظرة عامة

تم تحديث الكود لاستخدام Laravel Policies بدلاً من التحقق اليدوي للصلاحيات. هذا يوفر:

-   كود أكثر تنظيماً وقابلية للصيانة
-   إعادة استخدام منطق الصلاحيات
-   اختبار أسهل للصلاحيات
-   فصل منطق الأعمال عن منطق الصلاحيات

## الـ Policies المُحدثة

### 1. ProjectsPolicy

```php
class ProjectsPolicy {
    public function viewAny(User $user): bool {
        return true; // يمكن للجميع رؤية قائمة المشاريع
    }

    public function view(User $user, Projectss $projectss): bool {
        // يمكن للمدير أو مدير المشروع المخصص رؤية المشروع
        return $user->isAdmin() || $projectss->project_manager_id === $user->id;
    }

    public function create(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function update(User $user, Projectss $projectss): bool {
        // يمكن للمدير أو مدير المشروع المخصص تحديث المشروع
        return $user->isAdmin() || $projectss->project_manager_id === $user->id;
    }

    public function delete(User $user, Projectss $projectss): bool {
        // يمكن للمدير أو مدير المشروع المخصص حذف المشروع
        return $user->isAdmin() || $projectss->project_manager_id === $user->id;
    }

    public function assignProjectManager(User $user): bool {
        // فقط المدير يمكنه تعيين مديري مشاريع
        return $user->isAdmin();
    }
}
```

### 2. UsersPolicy

```php
class UsersPolicy {
    public function viewAny(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function view(User $user, User $users): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function create(User $user): bool {
        return $user->isAdmin() || $user->isProjectManager();
    }

    public function update(User $user, User $users): bool {
        return $user->isAdmin() || ($user->isProjectManager() && $user->manager_id == $user->id);
    }

    public function delete(User $user, User $users): bool {
        return false; // لا يمكن حذف المستخدمين
    }
}
```

## كيفية الاستخدام في Controllers

### الطريقة القديمة (التحقق اليدوي):

```php
public function update(Request $request, Projectss $project): JsonResponse {
    $user = Auth::user();

    // التحقق اليدوي
    if ($user->type !== 'admin' && $project->project_manager_id !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not have permission to update this project.'
        ], 403);
    }

    // باقي الكود...
}
```

### الطريقة الجديدة (استخدام Policies):

```php
public function update(Request $request, Projectss $project): JsonResponse {
    $user = Auth::user();

    // التحقق من الصلاحيات باستخدام Policy
    if (!Gate::allows('update', $project)) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. You do not have permission to update this project.'
        ], 403);
    }

    // باقي الكود...
}
```

## طرق استخدام الـ Policies

### 1. استخدام Gate::allows()

```php
if (!Gate::allows('update', $project)) {
    // رفض الطلب
}
```

### 2. استخدام Gate::authorize()

```php
Gate::authorize('update', $project);
// إذا لم يكن لديه صلاحية، سيتم رمي AuthorizationException تلقائياً
```

### 3. استخدام @can في Blade Views

```blade
@can('update', $project)
    <button>تحديث المشروع</button>
@endcan
```

### 4. استخدام can() في Controllers

```php
if ($request->user()->can('update', $project)) {
    // يمكنه التحديث
}
```

## تسجيل الـ Policies

تم تسجيل الـ Policies في `AppServiceProvider`:

```php
public function boot(): void {
    // تسجيل الـ Policies
    Gate::policy(Projectss::class, ProjectsPolicy::class);
    Gate::policy(User::class, UsersPolicy::class);
}
```

## مزايا استخدام الـ Policies

1. **إعادة الاستخدام**: يمكن استخدام نفس منطق الصلاحيات في عدة أماكن
2. **الاختبار**: سهولة اختبار منطق الصلاحيات بشكل منفصل
3. **الصيانة**: تغيير منطق الصلاحيات في مكان واحد فقط
4. **الوضوح**: كود أكثر وضوحاً وقابلية للقراءة
5. **الأمان**: تقليل احتمالية الأخطاء في منطق الصلاحيات

## أمثلة إضافية

### التحقق من صلاحيات خاصة

```php
// التحقق من صلاحية تعيين مدير المشروع
if (!Gate::allows('assignProjectManager', $user) && $validated['project_manager_id'] != $user->id) {
    return response()->json([
        'success' => false,
        'message' => 'You can only assign yourself as project manager.'
    ], 403);
}
```

### استخدام في Middleware

يمكن إنشاء Middleware خاص للتحقق من الصلاحيات:

```php
class CheckProjectPermission
{
    public function handle($request, Closure $next, $ability)
    {
        $project = $request->route('project');

        if (!Gate::allows($ability, $project)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return $next($request);
    }
}
```

## الخلاصة

استخدام Laravel Policies يوفر طريقة أكثر احترافية وأماناً للتعامل مع الصلاحيات في التطبيق. هذا يجعل الكود أكثر تنظيماً وقابلية للصيانة والاختبار.
