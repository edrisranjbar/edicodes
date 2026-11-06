"""گرادیان کاهشی روی یک هزینهٔ اسکالر؛ نمونهٔ آموزشی، نه مدل عمومی."""
import json


def descent(rate=0.1, steps=12):
    weight = 0.0
    history = [{"step": 0, "weight": weight, "loss": (weight - 2) ** 2}]
    for step in range(1, steps + 1):
        gradient = 2 * (weight - 2)
        weight -= rate * gradient
        history.append({"step": step, "weight": weight, "loss": (weight - 2) ** 2})
    return history


def run_demo():
    history = descent()
    assert abs(history[1]["weight"] - 0.4) < 1e-12
    assert abs(history[2]["loss"] - 1.6384) < 1e-12
    assert all(b["loss"] <= a["loss"] for a, b in zip(history, history[1:]))
    assert descent(rate=1.2)[-1]["loss"] > 4
    return {"first_three": history[:3], "final": history[-1],
            "large_rate_final_loss": descent(rate=1.2)[-1]["loss"]}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
