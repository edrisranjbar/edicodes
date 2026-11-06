"""ماتریس درهم‌ریختگی و آستانه؛ احتمال‌ها و برچسب‌ها ساختگی‌اند."""
import json


def evaluate(actual, scores, threshold):
    pairs = list(zip(actual, [int(s >= threshold) for s in scores]))
    tp = sum(a == 1 and p == 1 for a, p in pairs)
    fp = sum(a == 0 and p == 1 for a, p in pairs)
    fn = sum(a == 1 and p == 0 for a, p in pairs)
    tn = sum(a == 0 and p == 0 for a, p in pairs)
    precision = tp / (tp + fp) if tp + fp else None
    recall = tp / (tp + fn) if tp + fn else None
    f1 = 2 * tp / (2 * tp + fp + fn) if 2 * tp + fp + fn else None
    return dict(tp=tp, fp=fp, fn=fn, tn=tn, precision=precision,
                recall=recall, f1=f1, accuracy=(tp + tn) / len(pairs))


def run_demo():
    actual = [0, 1, 0, 1, 0, 1]
    scores = [0.1, 0.3, 0.4, 0.6, 0.7, 0.9]
    result = evaluate(actual, scores, 0.5)
    assert [result[k] for k in ["tp", "fp", "fn", "tn"]] == [2, 1, 1, 2]
    assert evaluate(actual, scores, 1.1)["precision"] is None
    return result


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
